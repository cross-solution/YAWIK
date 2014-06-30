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

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ApplyController extends AbstractActionController
{
    
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
    }
    
    public function preDispatch(MvcEvent $e)
    {
        $request      = $this->getRequest();
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $action       = false;
        
        if ($request->isPost()) {
            $appId = $this->params()->fromPost('id');
            if (!$appId) {
                throw new \RuntimeException('Missing application id.');
            }
            $application = $repository->find($appId);
            if (!$application) {
                throw new \RuntimeException('Invalid application id.');
            }
            
            $action     = 'process' . ($request->isXmlHttpRequest() ? 'part' : 'form');
            $routeMatch = $e->getRouteMatch();
            $routeMatch->setParam('action', $action);
            
        } else {
            
        }
        
        if (false !== $action) {
            
        }
    }
    
    protected function createJobNotFoundModel()
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
        $appId = $this->params('applyId');
        if (!$appId) {
            throw new \RuntimeException('Missing apply id');
        }
        
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        
        $job = $repositories->get('Jobs/Job')->findOneByApplyId($appId);
        
        if (!$job) {
            return $this->createJobNotFoundModel();
        }

        $user = $this->auth()->getUser();
        $application = new Application();
        $application->setIsDraft(true);
        $application->setJob($job);
        $application->setContact($user->info);
        $application->setUser($user);
        
        //$repositories->store($application);
        $form = $services->get('forms')->get('Applications/Apply');
        
        //$form = $this->getServiceLocator()->get('forms')->get('Application/Create');
        $form->bind($application);
        return array(
            'form' => $form
        );
    }
}
