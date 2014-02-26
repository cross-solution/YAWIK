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

    public function setEventManager(EventManagerInterface $eventManager)
    {
        parent::setEventManager($eventManager);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'checkAcl'));
        return $this;
    }
    
    public function checkAcl()
    {
        if (!$this->acl()->isRole('recruiter')) {
            throw new UnauthorizedAccessException('Only recruiter are allowed to manage jobs.');
        }
    }
    
    /**
     * Action called, when a new job should be created.
     * 
     */
    public function newAction()
    {
       $form = $this->getFormular($this->getJob());
       return $this->getViewModel('form', array(
           'form' => $form,
           'action' => 'new',
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
                $this->getServiceLocator()->get('repositories')->persist($job);
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
        $form     = $forms->get('Jobs/Job');
        $form->bind($job);

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

