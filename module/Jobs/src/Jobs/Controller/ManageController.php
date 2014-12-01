<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
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
        $pageToForm         = array(0 => 'locationForm', 1 => 'descriptionForm',  2 => 'previewForm');
        $request            = $this->getRequest();
        $params             = $this->params();
        $formIdentifier     = $params->fromQuery('form');
        $pageIdentifier     = (int) $params->fromQuery('page',0);
        $jobEntity          = $this->getJob();
        //$this->acl($job, $origAction);
        $form               = $this->getFormular($jobEntity);
        $mvcEvent           = $this->getEvent();
        // getting and setting the active form
        //$formIdentifier = "locationForm";
        if (isset($pageIdentifier)) {
            if (array_key_exists($pageIdentifier, $pageToForm)) {
                $actualFormIdentifier = $pageToForm[$pageIdentifier];
                $form->enableForm($actualFormIdentifier);
            }
            else {
                throw new \RuntimeException('No form found for page ' . $pageIdentifier);
            }
        }
        $pageLinkNext = Null;
        $pageLinkPrevious = Null;
        if (0 < $pageIdentifier) {
            $pageLinkPrevious = $this->url()
                                     ->fromRoute(
                                     'lang/jobs/manage',
                                         array(),
                                         array(
                                             'query' => array(
                                                 'id'   => $jobEntity->id,
                                                 'page' => $pageIdentifier - 1
                                             )
                                         )
            );
        }
        if ($pageIdentifier < count($pageToForm) - 1) {
            $pageLinkNext = $this->url()->fromRoute(
                                 'lang/jobs/manage',
                                     array(
                                     ),
                                     array(
                                         'query' => array(
                                             'id' => $jobEntity->id,
                                             'page' => $pageIdentifier + 1
                                         )
                                     )
            );
        }


        //$formActive         = $form->getActiveFormActual();
        //if (empty($formIdentifier)) {
        //    $form->enableForm($formActive);
        //}
        if (isset($formIdentifier) &&  $request->isPost()) {
            // at this point the form get instanciated and immediately accumulated
            $instanceForm = $form->get($formIdentifier);
            if (!isset($instanceForm)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            // the id is part of the postData, but it never should be altered
            $postData = $request->getPost();
            unset($postData['id']);
            unset($postData['applyId']);
            $instanceForm->setData($postData);
            if ($instanceForm->isValid()) {
                $title = $jobEntity->title;
                $templateTitle = $jobEntity->templateValues->title;
                if (empty($templateTitle)) {
                    $jobEntity->templateValues->title = $title;
                }
                $this->getServiceLocator()->get('repositories')->persist($jobEntity);
                $this->notification()->success(/*@translate*/ 'Job saved');
            } else {
                $this->notification()->error(/*@translate*/ 'There were errors in the form');
            }
        }
        $viewModel = $this->getViewModel($form);
        //$viewModel->setVariable('page_next', 1);
        $viewModel->setVariables(array(
            'pageLinkPrevious' => $pageLinkPrevious,
            'pageLinkNext' => $pageLinkNext,
            'page' => $pageIdentifier,
            'title' => $jobEntity->title
        ));
        return $viewModel;
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
        $container->disableForm();
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
        $isAjax               = $request->isXmlHttpRequest();
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
            unset($postData['applyId']);
            $instanceForm->setData($postData);
            if ($instanceForm->isValid()) {
                $this->getServiceLocator()->get('repositories')->persist($jobEntity);
            }
        }

        if (!$isAjax) {
            $basePath   = $viewHelperManager->get('basepath');
            $headScript = $viewHelperManager->get('headscript');
            $headScript->appendFile($basePath->__invoke('/Core/js/core.forms.js'));
            //$headScript->appendScript('$(document).ready(function() { $() });');
        }
        else {
            return new JsonModel(array('valid' => True));
        }

        $descriptionFormBenefits = $formTemplate->get('descriptionFormBenefits');
        $renderedDescriptionFormBenefits = $viewHelperForm->render($descriptionFormBenefits);

        $descriptionFormRequirements = $formTemplate->get('descriptionFormRequirements');
        $renderedDescriptionFormRequirements = $viewHelperForm->render($descriptionFormRequirements);

        $descriptionFormQualifications = $formTemplate->get('descriptionFormQualifications');
        $renderedDescriptionFormQualifications = $viewHelperForm->render($descriptionFormQualifications);

        $descriptionFormTitle = $formTemplate->get('descriptionFormTitle');
        $renderedDescriptionFormTitle = $viewHelperForm->render($descriptionFormTitle);

        // http://yawik.org/demo/de/apply/software-developer?subscriberUri=http%3A%2F%2Fcross-solution.de%2Fsubscriber%2F2
        $uriApply = $jobEntity->uriApply;
        if (empty($uriApply)) {
            $uriApply = $this->url()->fromRoute('lang/apply', array('applyId' => $jobEntity->applyId));
        }
        //$this->url('lang/apply', array('applyId' => 'software-developer')

        $model->setTemplate('templates/default/index.phtml');
        $applicationViewModel->setTemplate('iframe/iFrameInjection');
        $model->setVariables(array(
            'benefits' => $renderedDescriptionFormBenefits,
            'requirements' => $renderedDescriptionFormRequirements,
            'qualifications' => $renderedDescriptionFormQualifications,
            'title' => $renderedDescriptionFormTitle,
            'uriApply' => $uriApply
        ));

        return $model;
    }


}

