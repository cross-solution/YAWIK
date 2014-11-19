<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        $serviceLocator = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events = $this->getEventManager();
        $events->attach($defaultServices);

        return $this;
    }

    /**
     * Action called, when a new job should be created.
     * 
     */
    public function newAction()
    {
        $job = $this->getJob(/* create */ true);
        $this->acl($job, 'new');
        $user = $this->auth()->getUser();
        $job->setContactEmail($user->info->email);
        $job->setApplyId(
            uniqid(substr(md5($user->login), 0, 3))
        );
        $form  = $this->getFormular($job); 
        $model = $this->getViewModel($form);
        
        return $model;
    }

    // @TODO edit-Action and save-Action are doing the same, one of them has to quit
    public function editAction()
    {
        return $this->save();
    }
    
    public function saveAction()
    {
        return $this->save();
    }

    protected function save()
    {
        $request            = $this->getRequest();
        $params             = $this->params();
        $formIdentifier     = $params->fromQuery('form');
        $jobEntity          = $this->getJob();
        //$this->acl($job, $origAction);
        $form       = $this->getFormular($jobEntity);
        if (isset($formIdentifier) &&  $request->isPost()) {
            // at this point the form get instanciated and immediately accumulated
            $instanceForm = $form->get($formIdentifier);
            if (!isset($instanceForm)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            // the id is part of the postData, but it never should be altered
            $postData = $request->getPost();
            unset($postData['id']);
            $instanceForm->setData($postData);
            if ($instanceForm->isValid()) {
                $this->getServiceLocator()->get('repositories')->persist($jobEntity);
                $this->notification()->success(/*@translate*/ 'Job saved');
            }
            else {
                $this->notification()->error(/*@translate*/ 'There were errors in the form');
            }
        }

        return $this->getViewModel($form);
    }
    
    public function checkApplyIdAction()
    {
        $services = $this->getServiceLocator();
        $validator = $services->get('validatormanager')->get('Jobs/Form/UniqueApplyId');
        if (!$validator->isValid($this->params()->fromQuery('applyId'))) {
            return array(
                'ok' => false,
                'messages' => $validator->getMessages(),
            );
        }
        return array('ok' => true);
    }
    
    protected function getFormular($job)
    {
        $services = $this->getServiceLocator();
        $forms    = $services->get('FormElementManager');
        $container = $forms->get('Jobs/Job', array(
            'mode' => $job->id ? 'edit' : 'new'
        ));
        $container->setEntity($job);
        $container->setParam('job',$job->id);
        $container->setParam('applyId',$job->applyId);
        return $container;
        /*
        $formTitleLocation = $form->getForm->get('location');
        $formTitleLocation->bind($job);
        
        if ($this->getRequest()->isPost()) {
            $formTitleLocation->setData($_POST);
        }

        return $formTitleLocation;
        */
    }
    
    protected function getJob($allowDraft = true)
    {
        $services       = $this->getServiceLocator();
        $repositories   = $services->get('repositories');
        $repository     = $repositories->get('Jobs/Job');
        // @TODO three different method to obtain the job-id ?, simplify this
        $id_fromRoute   = $this->params('id',0);
        $id_fromQuery   = $this->params()->fromQuery('id',0);
        $id_fromSubForm = $this->params()->fromPost('job',0);
        $id             = empty($id_fromRoute)? (empty($id_fromQuery)?$id_fromSubForm:$id_fromQuery) : $id_fromRoute;
        
        if (empty($id) && $allowDraft) {
            $job        = $repository->create();
            $user       = $this->auth()->getUser();
            $job->setIsDraft(true);
            $job->setUser($user);
            $repositories->store($job);
            return $job;
        }

        $jobEntity      = $repository->find($id);
        if (!$jobEntity) {
            throw new \RuntimeException('No job found with id "' . $id . '"');
        }
        return $jobEntity;
    }
    
    protected function getViewModel($form, array $params = array())
    {
        $variables = array(
            'form' => $form,
        );
        $viewVars  = array_merge($variables, $params);
        
        $model = new ViewModel($viewVars);
        $model->setTemplate("jobs/manage/form");
        
        return $model;
    }

    protected function get($key) {
        return;
    }


    protected function edittemplateAction()
    {
        $request              = $this->getRequest();
        $params               = $this->params();
        $formIdentifier       = $params->fromQuery('form');
        $services             = $this->getServiceLocator();
        $viewHelperManager    = $services->get('ViewHelperManager');
        $viewHelperForm       = $viewHelperManager->get('formsimple');
        $mvcEvent             = $this->getEvent();
        $id                   = $this->params('id');
        $applicationViewModel = $mvcEvent->getViewModel();
        $repositories         = $services->get('repositories');
        $repositoryJob        = $repositories->get('Jobs/Job');
        $jobEntity            = $repositoryJob->find($id);
        $model                = new ViewModel();
        $forms                = $services->get('FormElementManager');
        $formTemplate         = $forms->get('Jobs/Description/Template', array(
                                    'mode' => $jobEntity->id ? 'edit' : 'new'
                                ));

        $formTemplate->setParam('id', $jobEntity->id);
        $formTemplate->setParam('applyId', $jobEntity->applyId);
        $formTemplate->setEntity($jobEntity);

        if (isset($formIdentifier) && $request->isPost()) {
            // at this point the form get instanciated and immediately accumulated
            $instanceForm = $formTemplate->get($formIdentifier);
            if (!isset($instanceForm)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            // the id is part of the postData, but it never should be altered
            $postData = $request->getPost();
            unset($postData['id']);
            $instanceForm->setData($postData);
            if ($instanceForm->isValid()) {
                $this->getServiceLocator()->get('repositories')->persist($jobEntity);
            }
        }

        $descriptionFormBenefits = $formTemplate->get('descriptionFormBenefits');
        $renderedDescriptionFormBenefits = $viewHelperForm->render($descriptionFormBenefits);

        $descriptionFormRequirements = $formTemplate->get('descriptionFormRequirements');
        $renderedDescriptionFormRequirements = $viewHelperForm->render($descriptionFormRequirements);

        $descriptionFormQualifications = $formTemplate->get('descriptionFormQualifications');
        $renderedDescriptionFormQualifications = $viewHelperForm->render($descriptionFormQualifications);

        $descriptionFormTitle = $formTemplate->get('descriptionFormTitle');
        $renderedDescriptionFormTitle = $viewHelperForm->render($descriptionFormTitle);

        $model->setTemplate('templates/default/index.phtml');
        $applicationViewModel->setTemplate('iframe/iFrameInjection');
        $model->setVariables(array(
            'benefits' => $renderedDescriptionFormBenefits,
            'requirements' => $renderedDescriptionFormRequirements,
            'qualifications' => $renderedDescriptionFormQualifications,
            'title' => $renderedDescriptionFormTitle,
        ));

        return $model;
    }


}

