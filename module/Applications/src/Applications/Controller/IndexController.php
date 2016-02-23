<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controller */
namespace Applications\Controller;

use Auth\Entity\Info;
use Applications\Entity\Application;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Applications\Entity\Status;
use Applications\Entity\StatusInterface;

/**
 * Main Action Controller for Applications module.
 *
 * @method \Core\Controller\Plugin\Notification notification()
 * @method \Core\Controller\Plugin\CreatePaginator paginator()
 * @method \Auth\Controller\Plugin\Auth auth()
 * @method \Acl\Controller\Plugin\Acl acl()
 */
class IndexController extends AbstractActionController
{
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }


    /**
     * Processes formular data of the application form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $services = $this->getServiceLocator();
        /** @var Request $request */
        $request = $this->getRequest();

        $jobId = $this->params()->fromPost('jobId', 0);
        $applyId = (int) $this->params()->fromPost('applyId', 0);
        
        // subscriber comes from the form
        $subscriberUri = $this->params()->fromPost('subscriberUri', '');
        if (empty($subscriberUri)) {
            // subscriber comes with the request of the form
            // which implies that the backlink in the job-offer had such an link in the query
            $subscriberUri = $this->params()->fromQuery('subscriberUri', '');
        }
        if (empty($subscriberUri)) {
            // the subscriber comes from an external module, maybe after interpreting the backlink, or the referer
            $e = $this->getEvent();
            $subscriberResponseCollection = $this->getEventManager()->trigger('subscriber.getUri', $e);
            if (!$subscriberResponseCollection->isEmpty()) {
                $subscriberUri = $subscriberResponseCollection->last();
            }
        }


        $job = ($request->isPost() && !empty($jobId))
             ? $services->get('repositories')->get('Jobs/Job')->find($jobId)
             : $services->get('repositories')->get('Jobs/Job')->findOneBy(array("applyId"=>(0 == $applyId)?$this->params('jobId'):$applyId));
        
        
        if (!$job) {
            $this->response->setStatusCode(410);
            $model = new ViewModel(
                array(
                'content' => /*@translate*/ 'Invalid apply id'
                )
            );
            $model->setTemplate('auth/index/job-not-found.phtml');
            return $model;
        }

        /* @var $form \Zend\Form\Form */
        $form = $services->get('FormElementManager')->get('Application/Create');
        $form->setValidate();
        
        $viewModel = new ViewModel();
        $viewModel->setVariables(
            array(
            'job' => $job,
            'form' => $form,
            'isApplicationSaved' => false,
            'subscriberUri' => $subscriberUri,
            )
        );
        
        $applicationEntity = new Application();
        $applicationEntity->setJob($job);

        if ($this->auth()->isLoggedIn()) {
            // copy the contact info into the application
            $contact = new Info();
            $contact->fromArray(Info::toArray($this->auth()->get('info')));
            $applicationEntity->setContact($contact);
        }
        
        $form->bind($applicationEntity);
        $form->get('jobId')->setValue($job->id);
        $form->get('subscriberUri')->setValue($subscriberUri);
       
        if ($request->isPost()) {
            if ($returnTo = $this->params()->fromPost('returnTo', false)) {
                $returnTo = \Zend\Uri\UriFactory::factory($returnTo);
            }
            $services = $this->getServiceLocator();

            $data = array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            );
            
            if (!empty($subscriberUri) && $request->isPost()) {
                $subscriber = $services->get('repositories')->get('Applications/Subscriber')->findbyUriOrCreate($subscriberUri);
                $applicationEntity->subscriber = $subscriber;
            }
            
            $form->setData($data);
            
            if (!$form->isValid()) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(
                        array(
                        'ok' => false,
                        'messages' => $form->getMessages()
                        )
                    );
                }
                if ($returnTo) {
                    $returnTo->setQuery($returnTo->getQueryAsArray() + array('status' => 'failure'));
                    return $this->redirect()->toUrl((string) $returnTo);
                }
                $this->notification()->error(/*@translate*/ 'There were errors in the form.');

            } else {
                $auth = $this->auth();
            
                if ($auth->isLoggedIn()) {
                    // in instance user is logged in,
                    // and no image is uploaded
                    // take his profile-image as copy
                    $applicationEntity->setUser($auth->getUser());
                    $imageData = $form->get('contact')->get('image')->getValue();
                    if (UPLOAD_ERR_NO_FILE == $imageData['error']) {
                        // has the user an image
                        $image = $auth->getUser()->info->image;
                        if ($image) {
                            $repositoryAttachment = $services->get('repositories')->get('Applications/Attachment');

                            // this should provide a real copy, not just a reference
                            $contactImage = $repositoryAttachment->copy($image);
                            $applicationEntity->contact->setImage($contactImage);
                        } else {
                            $applicationEntity->contact->setImage(null); //explicitly remove image.
                        }
                    }
                }
                
                if (!$request->isXmlHttpRequest()) {
                    $applicationEntity->setStatus(new Status());
                    $permissions = $applicationEntity->getPermissions();
                    $permissions->inherit($job->getPermissions());

                    /*
                     * New Application alert Mails to job recruiter
                     * This is temporary until Companies are implemented.
                     */
                    $recruiter = $services->get('repositories')->get('Auth/User')->findOneByEmail($job->contactEmail);
                    if (!$recruiter) {
                        $recruiter = $job->user;
                        $admin     = false;
                    } else {
                        $admin     = $job->user;
                    }
                
                    $services->get('repositories')->store($applicationEntity);
                    /*
                     * New Application alert Mails to job recruiter
                     * This is temporary until Companies are implemented.
                     */
                    $recruiter = $services->get('repositories')->get('Auth/User')->findOneByEmail($job->contactEmail);
                    if (!$recruiter) {
                        $recruiter = $job->user;
                        $admin     = false;
                    } else {
                        $admin     = $job->user;
                    }
                
                    if ($recruiter->getSettings('Applications')->getMailAccess()) {
                        $this->mailer('Applications/NewApplication', array('job' => $job, 'user' => $recruiter, 'admin' => $admin), /*send*/ true);
                    }
                    if ($recruiter->getSettings('Applications')->getAutoConfirmMail()) {
                        $ackBody = $recruiter->getSettings('Applications')->getMailConfirmationText();
                        if (empty($ackBody)) {
                            $ackBody = $job->user->getSettings('Applications')->getMailConfirmationText();
                        }
                        if (!empty($ackBody)) {
                            /* Acknowledge mail to the applicant */
                            $ackMail = $this->mailer(
                                'Applications/Confirmation',
                                array('application' => $applicationEntity,
                                                  'body' => $ackBody,
                                            )
                            );
                            // Must be called after initializers in creation
                            $ackMail->setSubject(/*@translate*/ 'Application confirmation');
                            $ackMail->setFrom($recruiter->getInfo()->getEmail());
                            $this->mailer($ackMail);
                            $applicationEntity->changeStatus(StatusInterface::CONFIRMED, sprintf('Mail was sent to %s', $applicationEntity->contact->email));
                        }
                    }

                    // send carbon copy of the application

                    $paramsCC = $this->getRequest()->getPost('carboncopy', 0);
                    if (isset($paramsCC) && array_key_exists('carboncopy', $paramsCC)) {
                        $wantCarbonCopy = (int) $paramsCC['carboncopy'];
                        if ($wantCarbonCopy) {
                             $mail = $this->mailer(
                                 'Applications/CarbonCopy',
                                 array(
                                    'application' => $applicationEntity,
                                    'to'          => $applicationEntity->contact->email,
                                 ), /*send*/
                                 true
                             );
                        }
                    }
                }

                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(
                        array(
                        'ok' => true,
                        'id' => $applicationEntity->id,
                        'jobId' => $applicationEntity->job->id,
                        )
                    );
                }
                if ($returnTo) {
                    $returnTo->setQuery($returnTo->getQueryAsArray() + array('status' => 'success'));
                    return $this->redirect()->toUrl((string) $returnTo);
                }
                $this->notification()->success(/*@translate*/ 'your application was sent successfully');
                $viewModel->setVariable('isApplicationSaved', true);
            }
        }
        return $viewModel;
    }
    
    /**
     * Handles dashboard listings of applications
     *
     * @return array
     */
    public function dashboardAction()
    {
        $params = $this->getRequest()->getQuery();
        $isRecruiter = $this->acl()->isRole('recruiter');
        if ($isRecruiter) {
            $params->set('by', 'me');
        }

         //default sorting
        if (!isset($params['sort'])) {
            $params['sort']="-date";
        }
        $params->count = 5;
        $params->pageRange=5;

        $this->paginationParams()->setParams('Applications\Index', $params);

        $paginator = $this->paginator('Applications/Application', $params);
     
        return array(
            'script' => 'applications/index/dashboard',
            'applications' => $paginator
        );
    }

    /**
     * sends a test-mail with all application-data to the applicant
     * @return JsonModel
     */
    public function mailAction()
    {
        $services          = $this->getServiceLocator();
        $config            = $services->get('Config');
        $applicationId     = $this->getRequest()
                                  ->getQuery('id');
        $status            = $this->params('status');
        $repositories      = $services->get('repositories');
        $entityApplication = $repositories->get('Applications/Application')->find($applicationId);
        if (empty($entityApplication)) {
            $this->notification()->error(/*@translate*/ 'Application has been deleted.');
        } else {
            $jobEntity         = $entityApplication->job;
            $applicantEmail    = $entityApplication->contact->email;
            $organizationEmail = $jobEntity->contactEmail;
            $mailAddress        = null;
            switch ($status == 'test') {
                case 'company':
                    $mailAddress = $organizationEmail;
                    break;
                case 'test':
                default:
                    $mailAddress = $applicantEmail;
                    break;
            }
            if (!empty($mailAddress)) {
                $mailData = array(
                    'application' => $entityApplication,
                    'to'          => $mailAddress
                );
                if (array_key_exists('mails', $config) && array_key_exists('from', $config['mails']) && array_key_exists('email', $config['mails']['from'])) {
                    $mailData['from'] = $config['mails']['from']['email'];
                }

                $mail = $this->mailer('Applications/CarbonCopy', $mailData, true);
                $this->notification()
                     ->success(/*@translate*/ 'Mail has been send');
                if ($status == 'company') {
                    $repositories->remove($entityApplication);
                    $this->notification()->info(/*@translate*/ 'Application data has been deleted');
                }
            } else {
                $this->notification()->error(/*@translate*/ 'No mail adress available');
            }
        }
        return new JsonModel(array());
    }
}
