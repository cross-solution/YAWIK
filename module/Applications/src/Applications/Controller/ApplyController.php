<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Applications controllers */ 
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Applications\Entity\Application;
use Zend\View\Model\ViewModel;
use Auth\Entity\AnonymousUser;
use Zend\View\Model\JsonModel;
use Core\Form\Container;
use Core\Form\SummaryForm;
use Core\Entity\PermissionsInterface;
use Applications\Entity\Status;
use Applications\Entity\StatusInterface;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ApplyController extends AbstractActionController
{
    
    protected $container;
    
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
    }
    
    public function preDispatch(MvcEvent $e)
    {
        if ($this->params()->fromQuery('do')) {
            $e->getRouteMatch()->setParam('action', 'do');
            return;
        }
        
        $request      = $this->getRequest();
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $container    = $services->get('forms')->get('Applications/Apply');
        
        if ($request->isPost()) {
            $appId = $this->params()->fromPost('applicationId');
            if (!$appId) {
                throw new \RuntimeException('Missing application id.');
            }
            $application = $repository->find($appId);
            if (!$application) {
                throw new \RuntimeException('Invalid application id.');
            }
            $action     = 'process';
            $routeMatch = $e->getRouteMatch();
            $routeMatch->setParam('action', $action);
            
        } else {
            $user  = $this->auth()->getUser();
            $appId = $this->params('applyId');
            if (!$appId) {
                throw new \RuntimeException('Missing apply id');
            }
            $application = $repository->findDraft($user, $appId);
            if ($application) {
               $form = $container->getForm('contact.contact');
               $form->setDisplayMode('summary');
            } else {
                $job = $repositories->get('Jobs/Job')->findOneByApplyId($appId);
                
                if (!$job) {
                    $e->getRouteMatch()->setParam('action', 'job-not-found');
                    return;
                }
                
                $application = $repository->create();
                $application->setIsDraft(true)
                            ->setContact($user->info)
                            ->setUser($user)
                            ->setJob($job);
                $repositories->store($application);
                /*
                 * If we had copy an user image, we need to refresh its data
                 * to populate the length property.
                 */
                if ($image = $application->contact->image) {
                    $repositories->refresh($image);
                }
            }
        } 
        
        $container->setEntity($application);
        $this->container = $container;
    }
    
    public function jobNotFoundAction()
    {
        $this->response->setStatusCode(410);
        $model = new ViewModel(array(
            'content' => /*@translate*/ 'Invalid apply id'
        ));
        $model->setTemplate('auth/index/job-not-found.phtml');
        return $model;
    }
    
    public function indexAction()
    {
        $form        = $this->container;
        $application = $form->getEntity();
        
        $this->container->setParam('applicationId', $application->id);

        return array(
            'form' => $form,
            'isApplicationValid' => $this->checkApplication($application),
            'application' => $application,
        );
    }
    
    public function processAction()
    {
        $formName  = $this->params()->fromQuery('form');
        $form      = $this->container->getForm($formName);
        $postData  = $form->getOption('use_post_array') ? $_POST : array();
        $filesData = $form->getOption('use_files_array') ? $_FILES : array();
        $data      = array_merge($postData, $filesData);

        $form->setData($data);
        
        if (!$form->isValid()) {
            return new JsonModel(array(
                'valid' => false,
                'errors' => $form->getMessages(),
            ));
        }
        $application = $this->container->getEntity();
        $this->getServiceLocator()->get('repositories')->store($application);
        
        if ('file-uri' === $this->params()->fromPost('return')) {
            $content = $form->getHydrator()->getLastUploadedFile()->getUri();
        } else {
            if ($form instanceOf SummaryForm) {
                $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                $viewHelper = 'summaryform';
            } else {
                $viewHelper = 'form';
            }
            $content = $this->getServiceLocator()->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
        }
        
        return new JsonModel(array(
            'valid' => $form->isValid(),
            'content' => $content,
            'isApplicationValid' => $this->checkApplication($application)
        ));
    }
    
    public function doAction()
    {
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $application  = $repository->findDraft(
                            $this->auth()->getUser(),
                            $this->params('applyId')
                        );
        
        if (!$application) {
            throw new \Exception('No application draft found.');
        }
        
        if ('abort' == $this->params()->fromQuery('do')) {
            $repositories->remove($application);
            return $this->redirect()->toRoute('lang/apply', array('applyId' => $this->params('applyId')));
        }
        
        if (!$this->checkApplication($application)) {
            $this->notification()->error(/*@translate*/ 'There are missing required informations. Your application cannot be send.');
            return $this->redirect()->toRoute('lang/apply', array('applyId' => $this->params('applyId')));
        }
        
        $application->setIsDraft(false)
                    ->setStatus(new Status())
                    ->getPermissions()
                        ->revoke($this->auth()->getUser(), PermissionsInterface::PERMISSION_CHANGE)
                        ->inherit($application->getJob()->getPermissions());
        
        $this->sendRecruiterMails($application);
        $this->sendUserMails($application);
        
        $model = new ViewModel(array(
            'success' => true,
            'application' => $application,
        ));
        $model->setTemplate('applications/apply/index');
        return $model;
    }
    
    
    
    protected function checkApplication($application)
    {
        return '' != $application->contact->email 
               && $application->attributes->acceptedPrivacyPolicy;
    }
    
    protected function sendRecruiterMails($application)
    {
        $job = $application->getJob();
        $recruiter = $this->getServiceLocator()
                          ->get('repositories')
                          ->get('Auth/User')->findOneByEmail($job->contactEmail);
        
        if (!$recruiter) {
            $recruiter = $job->user;
            $admin     = false;
        } else {
            $admin     = $job->user;
        }
        
        $settings = $recruiter->getSettings('Applications');
        if ($settings->getMailAccess()) {
            $this->mailer('Applications/NewApplication', array('job' => $job, 'user' => $recruiter, 'admin' => $admin), /*send*/ true);
        }
        if ($settings->getAutoConfirmMail()) {
            $ackBody = $settings->getMailConfirmationText();
            if (empty($ackBody)) {
                $ackBody = $job->user->getSettings('Applications')->getMailConfirmationText();
            }
            if (!empty($ackBody)) {
        
                /* Acknowledge mail to applier */
                $ackMail = $this->mailer('Applications/Confirmation',
                    array('application' => $application,
                        'body' => $ackBody,
                    ));
                // Must be called after initializers in creation
                $ackMail->setSubject(/*@translate*/ 'Application confirmation');
                $ackMail->setFrom($recruiter->getInfo()->getEmail());
                $this->mailer($ackMail);
                $application->changeStatus(StatusInterface::CONFIRMED, sprintf('Mail was sent to %s' , $application->contact->email));
            }
        }
        
    }
    
    protected function sendUserMails($application)
    {
        if ($application->getAttributes()->getSendCarbonCopy()) {
            $mail = $this->mailer('Applications/CarbonCopy', array(
                'application' => $application,
            ), /*send*/ true);
        }
    }
}
