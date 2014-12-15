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
use Core\Form\SummaryForm;
use Auth\Exception\UnauthorizedAccessException;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Jobs\Listener\Events\JobEvent;
use Core\Form\SummaryFormInterface;
use Zend\Stdlib\ArrayUtils;


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
        $jobServices = $serviceLocator->get('Jobs/Listeners');
        $events = $this->getEventManager();
        $events->attach($defaultServices);
        $events->attach($jobServices);

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

    /**
     * save a Job-Post either by a regular request or by an async post (AJAX)
     * a mandatory parameter is the ID of the Job
     * in case of a regular Request you can
     *
     * @return null|ViewModel
     * @throws \RuntimeException
     */
    protected function save()
    {
        $serviceLocator     = $this->getServiceLocator();
        $request            = $this->getRequest();
        $isAjax             = $request->isXmlHttpRequest();
        $pageToForm         = array(0 => array('locationForm', 'nameForm', 'portalForm'), 1 => array('descriptionForm'),  2 => array('previewForm'));
        $request            = $this->getRequest();
        $params             = $this->params();
        $formIdentifier     = $params->fromQuery('form');
        $pageIdentifier     = (int) $params->fromQuery('page',0);
        $jobEntity          = $this->getJob();
        $viewModel          = Null;
        //$this->acl($job, $origAction);
        $form               = $this->getFormular($jobEntity);
        $mvcEvent           = $this->getEvent();
        // getting and setting the active form
        //$formIdentifier = "locationForm";
        $valid              = true;
        $instanceForm       = Null;
        $viewHelperManager  = $serviceLocator->get('ViewHelperManager');
        $formErrorMessages = array();
        if (isset($formIdentifier) &&  $request->isPost()) {
            // at this point the form get instantiated and immediately accumulated
            $instanceForm = $form->get($formIdentifier);
            if (!isset($instanceForm)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            // the id may be part of the postData, but it never should be altered
            $postData = $request->getPost();
            if (isset($postData['id'])) unset($postData['id']);
            unset($postData['applyId']);
            $instanceForm->setData($postData);
            $valid = $instanceForm->isValid();
            $formErrorMessages = ArrayUtils::merge($formErrorMessages, $instanceForm->getMessages());
            if ($valid) {
                $title = $jobEntity->title;
                $templateTitle = $jobEntity->templateValues->title;
                if (empty($templateTitle)) {
                    $jobEntity->templateValues->title = $title;
                }
                $serviceLocator->get('repositories')->persist($jobEntity);
            } else {
            }
        }

        // validation
        $jobValid = True;
        $errorMessage = array();
        $translator = $serviceLocator->get('mvcTranslator');
        if (empty($jobEntity->title)) {
            $jobValid = False;
            $errorMessage[] = $translator->translate('No Title');
        }
        if (empty($jobEntity->location)) {
            $jobValid = False;
            $errorMessage[] = $translator->translate('No Location');
        }
        if (empty($jobEntity->termsAccepted)) {
            $jobValid = False;
            $errorMessage[] = $translator->translate('Accept the Terms');
        }

        $errorMessage = '<br />' . implode('<br />', $errorMessage);
        if ($isAjax) {
            if ($instanceForm instanceOf SummaryForm)  {
                $instanceForm->setRenderMode(SummaryForm::RENDER_SUMMARY);
                $viewHelper = 'summaryform';
            } else {
                $viewHelper = 'form';
            }
            $content = $viewHelperManager->get($viewHelper)->__invoke($instanceForm);
            $viewModel = new JsonModel(array(
                'content' => $content,
                'valid' => $valid,
                'jobvalid' => $jobValid,
                'errors' => $formErrorMessages,
                'errorMessage' => $errorMessage));
        }
        else {
            if (isset($pageIdentifier)) {
                $form->disableForm();
                if (array_key_exists($pageIdentifier, $pageToForm)) {
                    foreach ($pageToForm[$pageIdentifier] as $actualFormIdentifier) {
                        $form->enableForm($actualFormIdentifier);
                        if ($jobEntity->isDraft()) {
                            $actualForm = $form->get($actualFormIdentifier);
                            if ($actualForm instanceOf SummaryFormInterface) {
                                $form->get($actualFormIdentifier)->setDisplayMode(SummaryFormInterface::RENDER_FORM);
                            }
                        }
                    }
                }
                else {
                    throw new \RuntimeException('No form found for page ' . $pageIdentifier);
                }
            }
            $pageLinkNext = Null;
            $pageLinkPrevious = Null;
            if (0 < $pageIdentifier) {
                $pageLinkPrevious = $this->url()->fromRoute('lang/jobs/manage', array(), array('query' => array('id' => $jobEntity->id, 'page' => $pageIdentifier - 1)));
            }
            if ($pageIdentifier < count($pageToForm) - 1) {
                $pageLinkNext     = $this->url()->fromRoute('lang/jobs/manage', array(), array('query' => array('id' => $jobEntity->id, 'page' => $pageIdentifier + 1)));
            }
            $completionLink = $this->url()->fromRoute('lang/jobs/completion', array('id' => $jobEntity->id));

            $viewModel = $this->getViewModel($form);
            //$viewModel->setVariable('page_next', 1);
            $viewModel->setVariables(array(
                'pageLinkPrevious' => $pageLinkPrevious,
                'pageLinkNext' => $pageLinkNext,
                'completionLink' => $completionLink,
                'page' => $pageIdentifier,
                'title' => $jobEntity->title,
                'job' => $jobEntity,
                'summary' => 'this is what we charge you for your offer...',
                'valid' => $valid,
                'jobvalid' => $jobValid,
                'errorMessage' => $errorMessage,

            ));
        }
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
        $container->setEntity($job);
        $container->setParam('job',$job->id);
        $container->setParam('applyId',$job->applyId);
        return $container;
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


    protected function editTemplateAction()
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

    public function completionAction() {

        $serviceLocator = $this->getServiceLocator();
        $jobEntity      = $this->getJob();
        $jobEvent       = $serviceLocator->get('Jobs/Event');
        $jobEvent->setJobEntity($jobEntity);
        $this->getEventManager()->trigger(JobEvent::EVENT_NEW, $jobEvent);
        $this->getEventManager()->trigger(JobEvent::EVENT_STATUS_CHANGED, $jobEvent);
        $this->getEventManager()->trigger(JobEvent::EVENT_SEND_PORTALS, $jobEvent);

        $jobEntity->isDraft = false;
        $jobEntity->status = 'active';
        $serviceLocator->get('repositories')->persist($jobEntity);

        return array('job' => $jobEntity);
    }

}

