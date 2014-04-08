<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Applications controller */
namespace Applications\Controller;

use Auth\Entity\Info;
use Applications\Entity\Application;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Applications\Entity\Status;
use Core\Entity\RelationEntity;
use Auth\Entity\User;

/**
 * Main Action Controller for Applications module.
 */
class IndexController extends AbstractActionController
{
    /**
     * handle the application form.
     * @todo document
     */
    public function indexAction()
    {           
        $services = $this->getServiceLocator();
        $request = $this->getRequest();

        $jobId = $this->params()->fromPost('jobId',0);
        $applyId = (int) $this->params()->fromPost('applyId',0);

        $job = ($request->isPost() && !empty($jobId))
             ? $services->get('repositories')->get('Jobs/Job')->find($jobId)
             : $services->get('repositories')->get('Jobs/Job')->findOneBy(array("applyId"=>(0 == $applyId)?$this->params('jobId'):$applyId));
        
        
        $form = $services->get('FormElementManager')->get('Application/Create');
        $form->setValidate();
        
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'job' => $job,
            'form' => $form,
            'isApplicationSaved' => false,
        ));
        
        $applicationEntity = new Application();
        $applicationEntity->setJob($job);
        
        if ($this->auth()->isLoggedIn()) {
            // copy the contact info into the application
            $contact = new Info();
            $contact->fromArray(Info::toArray($this->auth()->get('info')));
            $applicationEntity->setContact($contact);
        }
        
        $form->bind($applicationEntity);
        
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
                    $applicationEntity->setUser($auth->getUser());
                    $imageData = $form->get('contact')->get('image')->getValue();
                    if (UPLOAD_ERR_NO_FILE == $imageData['error']) {
                        $image = $auth->getUser()->info->image;
                        
                        if ($image) {
                            $contactImage = $services->get('repositories')->get('Applications/Files')->saveCopy($image);
                            $contactImage->addAllowedUser($job->user->id);
                            $applicationEntity->contact->setImage($contactImage);
                        } else {
                            $applicationEntity->contact->setImage(null); //explicitly remove image.
                        }
                    }
                }
                $applicationEntity->setStatus(new Status());
                $permissions = $applicationEntity->getPermissions();
                $permissions->inherit($job->getPermissions());
                
                $services->get('repositories')->store($applicationEntity);
                
                /*
                 * New Application alert Mails to job owner
                 */
                if ($job->user->getSettings('Applications')->getMailAccess()
                    && $job->user->info->email
                ) {
                    $this->mailer('Applications/NewApplication', array('job' => $job), /*send*/ true);
                }
                
                if ($this->auth()->isLoggedIn()) {
                    $userInfo = $this->auth()->get('info');
                    if (isset($userInfo)) {
                        // TODO: will dieser User eine Info haben (aus den Settings lesen)
                        $email = $userInfo->getEmail();
                        if (isset($email)) {
                            $userRel = $job->getUser();
                            //$user = $userRel->getEntity();
                            //$settings = $this->settings('auth', $user);
                            $settingsJobAuth = $this->settings('Auth', $job->getUser()->id);
                            if (isset($settingsJobAuth->mailText)) {
                                $mail = $this->mail();
                                $mail->addTo($email);
                                $mail->setBody($settingsJobAuth->mailText);
                                $mail->setFrom('cross@cross-solution.de', 'YAWIK');
                                $mail->setSubject('Bestätigung Bewerbung');
                                $result = $mail->send();
                            }  
                        }
                    }
                }
                    
                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(array(
                        'ok' => true,
                        'id' => $applicationEntity->id,
                        'jobId' => $applicationEntity->jobId,
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
        $paginator = $this->paginator('Applications/Application',$params);
     
        return array(
            'script' => 'applications/index/dashboard',
            #'type' => $this->params('type'),
            'applications' => $paginator
        );
    }
    
    
    /**
     * handle the privacy policy used in an application form.
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function disclaimerAction()
    { 
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}

