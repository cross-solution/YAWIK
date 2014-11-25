<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controller */
namespace Applications\Controller;

use Auth\Entity\Info;
use Applications\Entity\Application;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Applications\Entity\Status;
use Applications\Entity\StatusInterface;

/**
 * Main Action Controller for Applications module.
 *
 *
 * @method \Auth\Controller\Plugin\Auth auth
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
        $request = $this->getRequest();

        $jobId = $this->params()->fromPost('jobId',0);
        $applyId = (int) $this->params()->fromPost('applyId',0);
        
        // subscriber comes from the form
        $subscriberUri = $this->params()->fromPost('subscriberUri','');
        if (empty($subscriberUri)) {
            // subscriber comes with the request of the form
            // which implies that the backlink in the job-offer had such an link in the query
            $subscriberUri = $this->params()->fromQuery('subscriberUri','');
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
            $model = new ViewModel(array(
                'content' => /*@translate*/ 'Invalid apply id'
            ));
            $model->setTemplate('auth/index/job-not-found.phtml');
            return $model;
        }

        /* @var $form \Zend\Form\Form */
        $form = $services->get('FormElementManager')->get('Application/Create');
        $form->setValidate();
        
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'job' => $job,
            'form' => $form,
            'isApplicationSaved' => false,
            'subscriberUri' => $subscriberUri,
        ));
        
        $applicationEntity = new Application();
        $applicationEntity->setJob($job);
        //$a = $services->get('repositories')->get('Applications/Subscriber')->findOneBy(array( "uri" => "aaaa" ));

        if ($this->auth()->isLoggedIn()) {
            // copy the contact info into the application
            $contact = new Info();
            $contact->fromArray(Info::toArray($this->auth()->get('info')));
            $applicationEntity->setContact($contact);
        }
        
        $form->bind($applicationEntity);
        $form->get('jobId')->setValue($job->id);
        $form->get('subscriberUri')->setValue($subscriberUri);
        
        /*
         * validate email. 
         */
         /**
         * 
         * @todo has to be fixed  
         * does not work. Validation is set in \Auth\Form\UserInfoFieldset.php
         * 
         *  $form->getInputFilter()->get('contact')->get('email')->getValidatorChain()
                ->attach(new \Zend\Validator\EmailAddress())
                ->attach(new \Zend\Validator\StringLength(array('max'=>100)));
         */
       
        if ($request->isPost()) {
            if ($returnTo = $this->params()->fromPost('returnTo', false)) {
                $returnTo = \Zend\Uri\UriFactory::factory($returnTo);
            }
            $services = $this->getServiceLocator();
            $repository = $services->get('repositories')->get('Applications/Application');
            
            
            //$applicationEntity = $services->get('builders')->get('Application')->getEntity(); 
            //$form->bind($applicationEntity);
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
                    return new JsonModel(array(
                        'ok' => false,
                        'messages' => $form->getMessages()
                    ));
                }
                if ($returnTo) {
                    $returnTo->setQuery($returnTo->getQueryAsArray() + array('status' => 'failure'));
                    return $this->redirect()->toUrl((string) $returnTo);
                }
                $this->notification()->error(/*@translate*/ 'There were errors in the form.');
                //$form->populateValues($data);
            } else {
                $auth = $this->auth();
            
                if ($auth->isLoggedIn()) {
                    // in instance user is logged in,
                    // and no image is uploaded
                    // take his profil-image as copy
                    $applicationEntity->setUser($auth->getUser());
                    $imageData = $form->get('contact')->get('image')->getValue();
                    if (UPLOAD_ERR_NO_FILE == $imageData['error']) {
                        // has the user an image
                        $image = $auth->getUser()->info->image;
                        if ($image) {
                            //$contactImage = $services->get('repositories')->get('Applications/Application')->saveCopy($image);
                            //$contactImage->addAllowedUser($job->user->id);
                            //$applicationEntity->contact->setImage($contactImage);

                            $repositoryAttachment = $services->get('repositories')->get('Applications/Attachment');
                                    //->saveCopy($image);
                            // this should provide a real copy, not just a reference
                            $contactImage = $repositoryAttachment->copy($image);
                            //$contactImage->addAllowedUser($job->user->id);
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
                     * This is temporarly until Companies are implemented.
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
                     * This is temporarly until Companies are implemented.
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

                            /* Acknowledge mail to applier */
                            $ackMail = $this->mailer('Applications/Confirmation', 
                                            array('application' => $applicationEntity,
                                                  'body' => $ackBody,
                                            ));
                            // Must be called after initializers in creation
                            $ackMail->setSubject(/*@translate*/ 'Application confirmation');
                            $ackMail->setFrom($recruiter->getInfo()->getEmail());
                            $this->mailer($ackMail);
                            $applicationEntity->changeStatus(StatusInterface::CONFIRMED, sprintf('Mail was sent to %s' , $applicationEntity->contact->email));
                        }
                    }

                    // send carbon copy of the application
                    //$user = $auth->getUser();
                    $paramsCC = $this->getRequest()->getPost('carboncopy',0);
                    if (isset($paramsCC) && array_key_exists('carboncopy',$paramsCC)) {
                        $wantCarbonCopy = (int) $paramsCC['carboncopy'];
                        if ($wantCarbonCopy) {
                             $mail = $this->mailer('Applications/CarbonCopy', array(
                                    'application' => $applicationEntity,
                                    'to'          => $applicationEntity->contact->email,
                                    //'from'        => array($admin->info->email => $admin->info->displayName)
                                 ), /*send*/ true);
                        }
                    }
                }

                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(array(
                        'ok' => true,
                        'id' => $applicationEntity->id,
                        'jobId' => $applicationEntity->job->id,
                    ));
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
     * @return multitype:string unknown
     */
    public function dashboardAction()
    {
        $services = $this->getServiceLocator();
        $params = $this->getRequest()->getQuery();
        $isRecruiter = $this->acl()->isRole('recruiter');
        if ($isRecruiter) {
            $params->set('by', 'me');
        }
        
        $appRepo = $services->get('repositories')->get('Applications/Application');
         
         //default sorting
        if (!isset($params['sort'])) {
            $params['sort']="-date";
        }
        $params->count = 5;
        $this->paginationParams()->setParams('Applications\Index', $params);
        $paginator = $this->paginator('Applications/Application',$params);
     
        return array(
            'script' => 'applications/index/dashboard',
            #'type' => $this->params('type'),
            'applications' => $paginator
        );
    }
    
    
    /**
     * Handles the privacy policy used in an application form.
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function disclaimerAction()
    { 
        $viewModel = new ViewModel();
        if ($this->request->isXmlHttpRequest()) {
          $viewModel->setTerminal(true);
        }
        return $viewModel;
    }
}

