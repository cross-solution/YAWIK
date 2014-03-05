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
        
        
        /* This must run before onDispatch, because we could alter the action param */
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkPostRequest'), 10);
        
        return $this;
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
            $action = $routeMatch->getParam('action');
            $routeMatch->setParam('action', 'save');
            $routeMatch->setParam('origAction', $action);
        }
    }
    
    /**
     * Action called, when a new job should be created.
     * 
     */
    public function newAction()
    {
        $job = $this->getJob(/* create */ true);
        $this->acl($job, 'new');
        $job->contactEmail = $this->auth('info.email');
        $form = $this->getFormular($job); 
        return $this->getViewModel('form', array(
            'form' => $form,
            'action' => 'new',
        ));
    }
    
    public function editAction()
    {
        $job = $this->getJob();
        $this->acl($job, 'edit');
        $form = $this->getFormular($job);
        return $this->getViewModel('form', array(
            'form' => $form,
            'action' => 'edit',
            'job' => $job
        ));
    }
    
    public function saveAction()
    {
        $origAction = $this->params('origAction');
        $create     = 'new' == $origAction;
        $job        = $this->getJob($create);
        $this->acl($job, $origAction);
        $form       = $this->getFormular($job);
        
        if ($form->isValid()) {
            if ($create) {
                $this->flashMessenger()->addMessage(/*@translate*/ 'Job published.');
                $job->setStatus('active');
                $job->setUser($this->auth()->getUser());
                $this->getServiceLocator()->get('repositories')->persist($job);
            } else {
                $this->flashMessenger()->addMessage(/*@translate*/ 'Job saved.');
            }
            return $this->redirect()->toRoute('lang/jobs');
        }
        
        return $this->getViewModel('form', array(
            'form' => $form,
            'action' => $origAction,
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
    
    protected function getJob($create = false)
    {
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Jobs/Job');
        
        if ($create) {
            $job = $repository->create();
            return $job;
        }
        
        if ($this->getRequest()->isPost()) {
            $jobData = $this->params()->fromPost('job');
            $id      = isset($jobData['id']) ? $jobData['id'] : null;
        } else {
            $id = $this->params()->fromQuery('id');
        }

        if (!$id) {
            throw new \RuntimeException('Missing job id.');
        }

        $job = $repository->find($id);

        if (!$job) {
            throw new \RuntimeException('No job found with id "' . $id . '"');
        }

        return $job;
    }
    
    protected function getViewModel($template, array $variables = array())
    {
        $model = new ViewModel($variables);
        $model->setTemplate("jobs/manage/$template");
        
        return $model;
    }

}

