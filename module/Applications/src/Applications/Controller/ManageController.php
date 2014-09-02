<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controller */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as Session;
use Auth\Exception\UnauthorizedAccessException;
use Applications\Entity\StatusInterface as Status;
use Applications\Entity\Comment;
use Applications\Entity\Rating;
use Zend\Stdlib\Parameters;

/**
 * Handles managing actions on applications
 */
class ManageController extends AbstractActionController
{
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $action     = $this->params()->fromQuery('action');
        
        if ($routeMatch && $action) { 
            $routeMatch->setParam('action', $action);
        }

        return parent::onDispatch($e);
    }
    
    /**
     * List applications
     */
    public function indexAction()
    { 
        $translator = $this->getServiceLocator()->get('translator');
        $params = $this->paginationParams('Applications\Index', array(
            'page' => 1,
            'sort' => '-date',
            'search',
            'by',
            'job',
            'status',
            'unread' 
        ));
        
        $services              = $this->getServiceLocator();
        $jobRepository         = $services->get('repositories')->get('Jobs/Job');
        $applicationRepository = $services->get('repositories')->get('Applications/Application');
        //$url                   = $this->url()->fromRoute('lang/applications', array(), array('query' => 'clear=1'), true);
        $services_form         = $services->get('forms');
        $form                  = $services_form->get('Applications/Filter');
        $params                = $this->getRequest()->getQuery();
        $statusElement         = $form->get('status');
        $form->bind($params);
        
        $states                = $applicationRepository->getStates()->toArray();
        $states                = array_merge(array(/*@translate*/ 'all'), $states);
        
        $statesForSelections = array();
        foreach ($states as $state) {
            $statesForSelections[$state] = $state;
        }
        $statusElement->setValueOptions($statesForSelections);
        
        $job = $params->job ? $jobRepository->find($params->job)  : null;
        $paginator = $this->paginator('Applications/Application',$params);
                
        return array(
            'form' => $form,
            'applications' => $paginator,
            'byJobs' => 'jobs' == $params->get('by', 'me'),
            'sort' => $params->get('sort', 'none'),
            'search' => $params->get('search', ''),
            'job' => $job,
            'applicationStates' => $states,
            'applicationState' => $params->get('status', '')
        );
    }
    
    /**
     * Detail view of an application
     * 
     * @return Ambigous <\Zend\View\Model\JsonModel, multitype:boolean unknown >
     */
    public function detailAction(){

        if ('refresh-rating' == $this->params()->fromQuery('do')) {
            return $this->refreshRatingAction();
        }
        
        $nav = $this->getServiceLocator()->get('main_navigation');
        $page = $nav->findByRoute('lang/applications');
        $page->setActive();
        
        $repository = $this->getServiceLocator()->get('repositories')->get('Applications/Application');
        $application = $repository->find($this->params('id'));
        
        if (!$application) {
            
            $this->response->setStatusCode(410);
            $model = new ViewModel(array(
                'content' => /*@translate*/ 'Invalid apply id'
            ));
            $model->setTemplate('applications/error/not-found');
            return $model;
        }
        
    	$this->acl($application, 'read');
    	
    	$applicationIsUnread = false;
    	if ($application->isUnreadBy($this->auth('id'))) {
    	    $application->addReadBy($this->auth('id'));
    	    $applicationIsUnread = true;
    	}
    	
    	
        $format=$this->params()->fromQuery('format');

        if ($application->isDraft()) {
            $list = false;
        } else {
            $list = $this->paginationParams('Applications\Index', $repository);
            $list->setCurrent($application->id);
        }

        $return = array(
            'application'=> $application, 
            'list' => $list,
            'isUnread' => $applicationIsUnread
        );
        switch ($format) {
            case 'json':
                /*@deprecated - must be refactored */
                        $viewModel = new JsonModel();
                        $viewModel->setVariables(/*array(
                    'application' => */$this->getServiceLocator()
                                              ->get('builders')
                                              ->get('JsonApplication')
                                              ->unbuild($application)
                        );
                        $viewModel->setVariable('isUnread', $applicationIsUnread);
                $return = $viewModel;
                break;
            case 'pdf':
                $pdf = $this->getServiceLocator()->get('Core/html2pdf');
           
                break;
            default:
                $contentCollector = $this->getPluginManager()->get('Core/ContentCollector'); 
                $contentCollector->setTemplate('applications/manage/details/action-buttons');
                $actionButtons = $contentCollector->trigger('application.detail.actionbuttons', $application);
                
                $return = new ViewModel($return);
                $return->addChild($actionButtons, 'externActionButtons');
                break;
        }
        
        return $return;
    }
    
    /**
     * Refreshes the rating of an application
     * 
     * @throws \DomainException
     * @return \Zend\View\Model\ViewModel
     */
    public function refreshRatingAction()
    {
        $model = new ViewModel();
        $model->setTemplate('applications/manage/_rating');
        
        $application = $this->getServiceLocator()->get('repositories')->get('Applications/Application')
                        ->find($this->params('id', 0));
        
        if (!$application) {
            throw new \DomainException('Invalid application id.');
        }
        
        $model->setVariable('application', $application);
        return $model;
    }
    
    /**
     * Attaches a social profile to an application
     * @throws \InvalidArgumentException
     * @return multitype:unknown
     */
    public function socialProfileAction()
    {
        if ($spId = $this->params()->fromQuery('spId')) {
            $repositories = $this->getServiceLocator()->get('repositories');
            $repo = $repositories->get('Applications/Application');
            $profile = $repo->findProfile($this->params()->fromQuery('spId'));
            if (!$profile) {
                throw new \InvalidArgumentException('Could not find profile.');
            }
            
        } else if ($this->getRequest()->isPost()
                   && ($network = $this->params()->fromQuery('network'))
                   && ($data    = $this->params()->fromPost('data'))
        ) {
            $profileClass = '\\Auth\\Entity\\SocialProfiles\\' . $network;
            $profile      = new $profileClass();
            $profile->setData(\Zend\Json\Json::decode($data, \Zend\Json\Json::TYPE_ARRAY));
        } else {
            throw new \RuntimeException(
                'Missing arguments. Either provide "spId" as Get or "network" and "data" as Post.'
            );
        }
        
        return array(
            'profile' => $profile
        );
    }

    /**
     * Changes the status of an application
     * 
     * @return unknown|multitype:string |multitype:string unknown |multitype:unknown
     */
    public function statusAction()
    {
        $applicationId = $this->params('id');
        $repository    = $this->getServiceLocator()->get('repositories')->get('Applications/Application');
        $application   = $repository->find($applicationId);
        
        $this->acl($application, 'change');
        
        $jsonFormat    = 'json' == $this->params()->fromQuery('format');
        $status        = $this->params('status', Status::CONFIRMED);
        $settings = $this->settings();
        
        if (in_array($status, array(Status::INCOMING))) {
            $application->changeStatus($status);
            if ($this->request->isXmlHttpRequest()) {
                $response = $this->getResponse();
                $response->setContent('ok');
                return $response;
            }
            if ($jsonFormat) {
                return array(
                    'status' => 'success',
                );
            }
            return $this->redirect()->toRoute('lang/applications/detail', array(), true);
        }
       $mailService = $this->getServiceLocator()->get('Core/MailService');
       $mail = $mailService->get('Applications/StatusChange');
       $mail->setApplication($application);
       if ($this->request->isPost()) {
           $mail->setSubject($this->params()->fromPost('mailSubject'));
           $mail->setBody($this->params()->fromPost('mailText'));
           if ($from = $application->job->contactEmail) {
                $mail->setFrom($from, $application->job->company);
           }
           if ($this->settings()->mailBCC) {
               $user = $this->auth()->getUser();
               $mail->addBcc($user->info->email, $user->info->displayName);
           }
           $mailService->send($mail);
           
            $application->changeStatus($status, sprintf('Mail was sent to %s' , $application->contact->email));

            if ($jsonFormat) {
                return array(
                    'status' => 'success', 
                );
            }
            return $this->redirect()->toRoute('lang/applications/detail', array(), true);
        }
        
        $translator = $this->getServiceLocator()->get('translator');
        switch ($status) {
            default:
            case Status::CONFIRMED: $key = 'mailConfirmationText'; break;
            case Status::INVITED  : $key = 'mailInvitationText'; break;
            case Status::REJECTED : $key = 'mailRejectionText'; break;
        }
        $mailText      = $settings->$key ? $settings->$key : '';
        $mail->setBody($mailText);
        $mailText = $mail->getBodyText();
        $mailSubject   = sprintf(
            $translator->translate('Your application dated %s'),
            strftime('%x', $application->dateCreated->getTimestamp())
        );
        
        $params = array(
                'applicationId' => $applicationId,
                'status'        => $status,
                'mailSubject'   => $mailSubject,
                'mailText'      => $mailText        
            ); 
        if ($jsonFormat) {
            return $params;
        }
        
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Applications/Mail');
        $form->populateValues($params);
                
        return array(
            'form' => $form
        );
          
    } 
    
    /**
     * Forwards an application via Email
     * 
     * @throws \InvalidArgumentException
     * @return \Zend\View\Model\JsonModel
     */
    public function forwardAction()
    {
        $services     = $this->getServiceLocator();
        $emailAddress = $this->params()->fromQuery('email');
        $application  = $services->get('repositories')->get('Applications/Application')
                                 ->find($this->params('id'));
        
        $this->acl($application, 'forward');
        
        $translator   = $services->get('translator');
         
        if (!$emailAddress) {
            throw new \InvalidArgumentException('An email address must be supplied.');
        }
        
        $params = array(
            'ok' => true,
            'text' => sprintf($translator->translate('Forwarded application to %s'), $emailAddress)
        );
        
        try {
            $userName    = $this->auth('info')->displayName;
            $fromAddress = $application->job->contactEmail;
            $mailOptions = array(
                'application' => $application,
                'to'          => $emailAddress,
                'from'        => array($fromAddress => $userName)
            );
            $this->mailer('Applications/Forward', $mailOptions, true);
        } catch (\Exception $ex) {
            $params = array(
                'ok' => false,
                'text' => sprintf($translator->translate('Forward application to %s failed.'), $emailAddress)
            );
        }
        $application->changeStatus($application->status,$params['text']);
        return new JsonModel($params);
    }
    
    /**
     * Deletes an application
     * 
     * @throws \DomainException
     * @return multitype:string
     */
    public function deleteAction()
    {
        $id          = $this->params('id');
        $services    = $this->getServiceLocator();
        $repositories= $services->get('repositories');
        $repository  = $repositories->get('Applications/Application');
        $application = $repository->find($id);
        
        if (!$application) {
            throw new \DomainException('Application not found.');
        }
        
        $this->acl($application, 'delete');
        
        $repositories->remove($application);
        
        if ('json' == $this->params()->fromQuery('format')) {
            return array(
                'status' => 'success'
            );
        }
        
        $this->redirect()->toRoute('lang/applications', array(), true);
    }   
}
