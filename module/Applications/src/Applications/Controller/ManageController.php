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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as Session;
use Auth\Exception\UnauthorizedAccessException;
use Applications\Entity\StatusInterface as Status;




/**
 * Action Controller for managing applications.
 *
 */
class ManageController extends AbstractActionController
{
    
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
     *
     */
    public function indexAction()
    { 
        //echo $this->params()->getQuery('count'); exit;
        $params = $this->getRequest()->getQuery();
        $jsonFormat = 'json' == $params->get('format');
        
        if (!$jsonFormat) {
            $session = new Session('Applications\Index');
            if ($session->params) {
                foreach ($session->params as $key => $value) {
                    $params->set($key, $params->get($key, $value));
                }
            }
            $session->params = $params->toArray();
        }
        
        $v = new ViewModel(array(
            'by' => $params->get('by', 'me'),
            'hasJobs' => (bool) $this->getServiceLocator()
                                     ->get('repositories')
                                     ->get('job')
                                     ->countByUser($this->auth('id'))
        ));
        $v->setTemplate('applications/sidebar/manage');
        $this->layout()->addChild($v, 'sidebar_applicationsFilter');
        $repository = $this->getServiceLocator()->get('repositories')->get('application');
        
        $paginator = new \Zend\Paginator\Paginator(
            $repository->getPaginatorAdapter($params->toArray())
        );
        $paginator->setCurrentPageNumber($params->get('page', 1))
                  ->setItemCountPerPage($params->get('count', 10));
        
        if ($jsonFormat) {
            $viewModel = new JsonModel();
            //$items = iterator_to_array($paginator);
            
            $viewModel->setVariables(array(
                'items' => $this->getServiceLocator()->get('builders')->get('JsonApplication')
                                ->unbuildCollection($paginator->getCurrentItems()),
                'count' => $paginator->getTotalItemCount()
            ));
            return $viewModel;
            
        } 
        
        return array(
            'applications' => $paginator,
            'byJobs' => 'jobs' == $params->get('by', 'me'),
            'sort' => $params->get('sort', 'none'),
        );
        
        
    }
    
    public function detailAction(){

    	$application = $this->getServiceLocator()
    						->get('repositories')
    						->get('application')->find($this->params('id'), 'EAGER');
    	
    	$jsonFormat = 'json' == $this->params()->fromQuery('format');
    	if ($jsonFormat) {
    		$viewModel = new JsonModel();
    		$viewModel->setVariables(/*array(
    		    'application' => */$this->getServiceLocator()
    		                          ->get('builders')
    		                          ->get('JsonApplication')
    		                          ->unbuild($application)
    		);
    		return $viewModel;
    	}
        
    	$nav = $this->getServiceLocator()->get('main_navigation');
    	$page = $nav->findByRoute('lang/applications');
    	$page->setActive();
    	
    	return array('application'=> $application);
    }
    
    public function restAction() {
        $method = $this->params('method');
        $value = $this->params()->fromPost('value','');
        $key = $this->params('key');
        $user = $this->auth()->getUser();
        $result = array();   
        if (strcasecmp($key, 'mailtext') == 0) {
            $settingsJobAuth = $this->settings('auth', $user);
            if (strcasecmp($method, 'get') == 0) {
                $mailtext = $settingsJobAuth->getMailText();
                $result = array('result' => isset($mailtext)?$mailtext:'');
            }
            if (strcasecmp($method, 'set') == 0) {
                $settingsJobAuth->setAccessWrite(True);
                $settingsJobAuth->setMailText($value);
                $result = array('result' => $settingsJobAuth->getMailText());
                //$result['old'] = $value;
                //$result['post'] = $_POST;
                //$result['get'] = $_GET;
                //$result['server'] = $_SERVER;
                //$result['request'] = $_REQUEST;
            }
        }
        $viewModel = new JsonModel();
        $viewModel->setVariables($result);
        return $viewModel;
    }

    public function statusAction()
    {
        $applicationId = $this->params('id');
        $repository    = $this->getServiceLocator()->get('repositories')->get('Application');
        $application   = $repository->find($applicationId);
        $jsonFormat    = 'json' == $this->params()->fromQuery('format');
        $status        = $this->params('status', Status::CONFIRMED);
        
        if (in_array($status, array(Status::INCOMING))) {
            $application->changeStatus($status);
            $repository->save($application);
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
           $mailService->send($mail);
           
            // @todo must be in Mail-Controller-Plugin ::send()
            //       or in Plugin-Factory "MailFactory"
//             $mail->setEncoding('UTF-8');
//             $mail->getHeaders()->addHeader(\Zend\Mail\Header\ContentType::fromString('Content-Type: text/plain; charset=utf-8'));
            
//             $mail->setSubject($this->params()->fromPost('mailSubject'));
//             $mail->setBody($this->params()->fromPost('mailText'));
//             $from = $application->job->contactEmail
//                   ? $application->job->contactEmail
//                   : 'no-reply@bewerbermanagement.cross-solution.de';
//             $mail->setFrom($from, $application->job->company);
//             $mail->addTo($application->contact->email, $application->contact->displayName);
//             $mail->send();
            $application->changeStatus($status);
            $repository->save($application);
            
            if ($jsonFormat) {
                return array(
                    'status' => 'success', 
                );
            }
            return $this->redirect()->toRoute('lang/applications/detail', array(), true);
        }
        
        $translator = $this->getServiceLocator()->get('translator');
        $settings = $this->settings();
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
    
    public function deleteAction()
    {
        $id          = $this->params('id');
        $services    = $this->getServiceLocator();
        $repository  = $services->get('repositories')->get('Application');
        $application = $repository->find($id);
        
        if (!$application) {
            throw new \DomainException('Application not found.');
        }
        
        $this->acl($application, 'delete');
        
        $repository->delete($application);
        
        if ('json' == $this->params()->fromQuery('format')) {
            return array(
                'status' => 'success'
            );
        }
        
        $this->redirect()->toRoute('lang/applications', array(), true);
    }
    
}
