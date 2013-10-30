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
    
    /**
     * List applications
     *
     */
    public function indexAction()
    { 
        $params = $this->getRequest()->getQuery();
        $session = new Session('Applications\Index');
        if ($session->params) {
            foreach ($session->params as $key => $value) {
                if ('format' == $key) { continue; }
                $params->set($key, $params->get($key, $value));
            }
        }
        $session->params = $params->toArray();
        
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
                  ->setItemCountPerPage(10);
        
        
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        
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
        
        if (in_array($status, array(Status::CONFIRMED, Status::INCOMING))) {
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
        
        $mail          = $this->mail(array('application' => $application));
        
        if ($this->request->isPost()) {
            // @todo must be in Mail-Controller-Plugin ::send()
            //       or in Plugin-Factory "MailFactory"
            $mail->setEncoding('UTF-8');
            $mail->getHeaders()->addHeader(\Zend\Mail\Header\ContentType::fromString('Content-Type: text/plain; charset=utf-8'));
            
            $mail->setSubject($this->params()->fromPost('mailSubject'));
            $mail->setBody($this->params()->fromPost('mailText'));
            $from = $application->job->contactEmail
                  ? $application->job->contactEmail
                  : 'no-reply@bewerbermanagement.cross-solution.de';
            $mail->setFrom($from, $application->job->company);
            $mail->addTo($application->contact->email, $application->contact->displayName);
            $mail->send();
            $application->changeStatus($status);
            $repository->save($application);
            
            if ($jsonFormat) {
                return array(
                    'status' => 'success', 
                );
            }
            return $this->redirect()->toRoute('lang/applications/detail', array(), true);
        }
        
        $mail->template("$status-" . $this->params('lang'));
        $mailText      = $mail->getBody();
        $mailSubject   = $mail->getSubject();
        
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
    
}
