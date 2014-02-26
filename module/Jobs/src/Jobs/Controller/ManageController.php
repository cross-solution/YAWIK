<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Exception\UnauthorizedAccessException;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;


/**
 * This Controller handles management actions for jobs.
 *    
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ManageController extends AbstractActionController {

    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        
        
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkAcl'));
        /* This must run before onDispatch, because we could alter the action param */
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkPostRequest'), 10);
        
        return $this;
    }
    
    public function checkAcl()
    {
        if (!$this->acl()->isRole('recruiter')) {
            throw new UnauthorizedAccessException('Only recruiter are allowed to manage jobs.');
        }
    }
    
    public function checkPostRequest(MvcEvent $e)
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch) {
                return; // Let parent::onDispatch handle failure
            }
            /* All POST requests are handled by the saveAction! */
            $routeMatch->setParam('action', 'save');
        }
    }
    
    /**
     * Action called, when a new job should be created.
     * 
     */
    public function newAction()
    {
        $job = $this->getJob();
        $job->contactEmail = $this->auth('info.email');
        $form = $this->getFormular($job); 
        return $this->getViewModel('form', array(
            'form' => $form,
            'action' => 'new',
        ));
    }
    
    public function editAction()
    {
        $form = $this->getFormular($this->getJob());
        return $this->getViewModel('form', array(
            'form' => $form,
            'action' => 'edit',
        ));
    }
    
    public function saveAction()
    {
        $job  = $this->getJob();
        $form = $this->getFormular($job);
        if ($form->isValid()) {
            $this->flashMessenger()->addMessage(/*@translate*/ 'Job published.');
            if (!$job->id) {
                $job->setUser($this->auth()->getUser());
                $this->getServiceLocator()->get('repositories')->store($job);
            }
            return $this->redirect()->toRoute('lang/jobs');
        }
        
        return $this->getViewModel('form', array(
            'form' => $form,
            'action' => $job->id ? 'edit' : 'new',
            'hasErrors' => true
        ));
    }
    
    protected function getFormular($job)
    {
        $services = $this->getServiceLocator();
        $forms    = $services->get('FormElementManager');
        $form     = $forms->get('Jobs/Job', array(
            'mode' => $job->id ? 'edit' : 'new'
        ));
        $form->bind($job);
        
        if ($this->getRequest()->isPost()) {
            $form->setData($_POST);
        }

        return $form;
    }
    
    protected function getJob($id=null)
    {
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Jobs/Job');
        $id           = $this->getRequest()->isPost()
                      ? $this->params()->fromPost('id')
                      : $this->params()->fromQuery('id');

        $job          = $id
                      ? $repository->find($id)
                      : $repository->create();
        
        return $job;
    }
    
    protected function getViewModel($template, array $variables = array())
    {
        $model = new ViewModel($variables);
        $model->setTemplate("jobs/manage/$template");
        
        return $model;
    }

}

