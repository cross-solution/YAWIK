<?php
/**
 * Cross Applicant Management
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
        
        
        $form = $services->get('FormElementManager')->get('Application');
        
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
         
           $form->getInputFilter()->get('contact')->get('email')->getValidatorChain()
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
                //$form->populateValues($data);
            } else {
                $auth = $this->auth();
            
                if ($auth->isLoggedIn()) {
                    $applicationEntity->setUserId($auth('id'));
                    $imageData = $form->get('contact')->get('image')->getValue();
                    if (UPLOAD_ERR_NO_FILE == $imageData['error']) {
                        $image = $auth->getUser()->info->image->getEntity();
                        
                        if ($image) {
                            $contactImage = $services->get('repositories')->get('Applications/Files')->saveCopy($image);
                            $contactImage->addAllowedUser($job->userId);
                            $applicationEntity->contact->setImage($contactImage);
                        } else {
                            $applicationEntity->contact->setImage(null); //explicitly remove image.
                        }
                    }
                }
                $applicationEntity->setStatus(new Status());
                
                $services->get('repositories')->store($applicationEntity);
                
                /*
                 * New Application alert Mails to job owner
                 * @todo disabled until settings is migrated to doctrine
                 */
//                 $settings = $this->settings($job->user); 
//                 if ($email = $job->getContactEmail()) {
//                     $this->mailer('Applications/NewApplication', array('job' => $job), /*sendMail*/ true);
//                 }
                
                if ($this->auth()->isLoggedIn()) {
                    $userInfo = $this->auth()->get('info');
                    if (isset($userInfo)) {
                        // TODO: will dieser User eine Info haben (aus den Settings lesen)
                        $email = $userInfo->getEmail();
                        if (isset($email)) {
                            $userRel = $job->getUser();
                            //$user = $userRel->getEntity();
                            //$settings = $this->settings('auth', $user);
                            $settingsJobAuth = $this->settings('auth', $job->getUserid());
                            if (isset($settingsJobAuth->mailText)) {
                                $mail = $this->mail();
                                $mail->addTo($email);
                                $mail->setBody($settingsJobAuth->mailText);
                                $mail->setFrom('cross@cross-solution.de', 'Cross Applicant Management');
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
                $viewModel->setVariable('isApplicationSaved', true);
            }
        } 
        return $viewModel;
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

