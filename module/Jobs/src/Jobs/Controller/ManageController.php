<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Jobs\Entity\Status;
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
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
        $serviceLocator = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events = $this->getEventManager();
        $events->attach($defaultServices);

        return $this;
    }

    /**
     * Dispatch listener callback.
     *
     * Attachs the MailSender aggregate listener to the job event manager.
     *
     * @param MvcEvent $e
     * @since 0.19
     */
    public function preDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $action = $routeMatch->getParam('action');

        if (in_array($action, array('edit', 'approval', 'completion'))) {
            $services = $this->getServiceLocator();
            $jobEvents = $services->get('Jobs/Events');
            $mailSender = $services->get('Jobs/Listener/MailSender');

            $mailSender->attach($jobEvents);
        }
    }

    public function testAction()
    {
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
     * parameter are arbitrary elements for defaults or programming flow
     *
     * @param array $parameter
     * @return null|ViewModel
     * @throws \RuntimeException
     */
    protected function save($parameter = array())
    {
        $serviceLocator     = $this->getServiceLocator();
        $user               = $this->auth()->getUser();
        if (empty($user->info->email)) {
            return $this->getErrorViewModel('no-parent', array('cause' => 'noEmail'));
        }
        $userOrg            = $user->getOrganization();
        if (!$userOrg->hasAssociation()) {
            return $this->getErrorViewModel('no-parent', array('cause' => 'noCompany'));
        }
        $translator         = $serviceLocator->get('translator');
        /** @var \Zend\Http\Request $request */
        $request            = $this->getRequest();
        $isAjax             = $request->isXmlHttpRequest();
        $pageToForm         = array(0 => array('locationForm', 'nameForm', 'portalForm'),
                                    1 => array('descriptionForm'),
                                    2 => array('previewForm'));
        $request            = $this->getRequest();
        $params             = $this->params();
        $formIdentifier     = $params->fromQuery('form');
        $pageIdentifier     = (int) $params->fromQuery('page', array_key_exists('page', $parameter)?$parameter['page']:0);
        $jobEntity          = $this->getJob();
        $viewModel          = Null;
        $this->acl($jobEntity, 'edit');
        $form               = $this->getFormular($jobEntity);
        $mvcEvent           = $this->getEvent();

        $valid              = true;
        $instanceForm       = Null;
        $viewHelperManager  = $serviceLocator->get('ViewHelperManager');
        $formErrorMessages = array();
        if (isset($formIdentifier) &&  $request->isPost()) {
            // at this point the form get instantiated and immediately accumulated
            $instanceForm = $form->getForm($formIdentifier);
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
                    if (!$jobEntity->isDraft()) {
                        // Job is deployed, some changes are now disabled
                        $form->enableAll();
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
                'isDraft' => $jobEntity->isDraft()
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
        /** @var \Jobs\Repository\Job $repository */
        $repository     = $repositories->get('Jobs/Job');
        // @TODO three different method to obtain the job-id ?, simplify this
        $id_fromRoute   = $this->params('id',0);
        $id_fromQuery   = $this->params()->fromQuery('id',0);
        $id_fromSubForm = $this->params()->fromPost('job',0);
        $user           = $this->auth()->getUser();
        $id             = empty($id_fromRoute)? (empty($id_fromQuery)?$id_fromSubForm:$id_fromQuery) : $id_fromRoute;

        if (empty($id) && $allowDraft) {
            $this->acl('Jobs/Manage', 'new');
            /** @var \Jobs\Entity\Job $job */
            $job = $repository->findDraft($user);
            if (empty($job)) {

                $job = $repository->create();
                $job->setIsDraft(true);
                $job->setUser($user);
                $repositories->store($job);
            }
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

    /**
     * @param $key
     */
    protected function get($key) {
        return;
    }

    /**
     * Job opening is completed.
     *
     * @return array
     */
    public function completionAction() {

        $serviceLocator = $this->getServiceLocator();
        $jobEntity      = $this->getJob();

        $jobEntity->isDraft = false;
        $reference = $jobEntity->getReference();
        if (empty($reference)) {
            // create an unique job-reference
            $repository = $this->getServiceLocator()
                               ->get('repositories')
                               ->get('Jobs/Job');
            $jobEntity->setReference($repository->getUniqueReference());
        }
        $jobEntity->changeStatus(Status::CREATED, "job was created");
        $jobEntity->atsEnabled = true;

        /*
         * make the job opening persist and fire the EVENT_JOB_CREATED
         */
        $serviceLocator->get('repositories')->store($jobEntity);

        $jobEvents = $serviceLocator->get('Jobs/Events');
        $jobEvents->trigger(JobEvent::EVENT_JOB_CREATED, $this, array('job' => $jobEntity));

        return array('job' => $jobEntity);
    }

    /**
     * all actions around approve or decline jobs-offers
     *
     * @return array with the viewVariables
     */
    public function approvalAction() {

        $serviceLocator = $this->getServiceLocator();
        $translator     = $serviceLocator->get('translator');
        $user           = $this->auth()->getUser();
        $repositories   = $serviceLocator->get('repositories');
        $params         = $this->params('state');
        /** @var \Jobs\Entity\Job $jobEntity */
        $jobEntity      = $this->getJob();
        $jobEvent       = $serviceLocator->get('Jobs/Event');
        $jobEvent->setJobEntity($jobEntity);
        $jobEvents      = $serviceLocator->get('Jobs/Events');

        if ($params == 'declined') {
            $jobEntity->changeStatus(Status::REJECTED, sprintf( /*@translate*/ "Job opening was rejected by %s",$user->info->displayName));
            $jobEntity->isDraft = true;
            $repositories->store($jobEntity);
            $jobEvents->trigger(JobEvent::EVENT_JOB_REJECTED, $jobEvent);
            $this->notification()->success($translator->translate('Job has been rejected'));
        }

        if ($params == 'approved') {
            $jobEntity->changeStatus(Status::ACTIVE, sprintf( /*@translate*/ "Job opening was activated by %s",$user->info->displayName));
            $repositories->store($jobEntity);
            $jobEvents->trigger(JobEvent::EVENT_JOB_ACCEPTED, $jobEvent);
            $this->notification()->success($translator->translate('Job has been approved'));
        }

        $viewLink = $this->url()->fromRoute('lang/jobs/view',
            array(),
            array('query' =>
                      array( 'id' => $jobEntity->id)));

        $approvalLink = $this->url()->fromRoute('lang/jobs/approval',
            array('state' => 'approved'),
            array('query' =>
                      array( 'id' => $jobEntity->id)));

        $declineLink = $this->url()->fromRoute('lang/jobs/approval',
            array('state' => 'declined'),
            array('query' =>
                      array( 'id' => $jobEntity->id)));

        return array('job' => $jobEntity,
                     'viewLink' => $viewLink,
                     'approvalLink' => $approvalLink,
                     'declineLink' => $declineLink);
    }

    public function deactivateAction() {
        $serviceLocator = $this->getServiceLocator();
        $translator     = $serviceLocator->get('translator');
        $user           = $this->auth()->getUser();
        $jobEntity      = $this->getJob();

        try {
            $jobEntity->changeStatus(Status::INACTIVE, sprintf( /*@translate*/ "Job was deactivated by %s",$user->info->displayName));
            $this->notification()->success($translator->translate('Job has been deactivated'));
        } catch (\Exception $e) {
            $this->notification()->danger($translator->translate('Job could not be deactivated'));
        }
        return $this->save(array('page' => 2));
    }

    public function templateAction() {
        $serviceLocator          = $this->getServiceLocator();
        try {
            $jobEntity           = $this->getJob();
            $template            = $this->params('template','default');
            $repositories        = $serviceLocator->get('repositories');

            $translator          = $serviceLocator->get('translator');
            $jobEntity->template = $template;
            $repositories->store($jobEntity);
            $this->notification()->success($translator->translate('Template changed'));
        }
        catch (\Exception $e) {
            $this->notification()->danger($translator->translate('Template not changed'));
        }

        return new JsonModel(array());
    }

    protected function getErrorViewModel($script, $parameter = array())
    {
        $this->getResponse()->setStatusCode(500);

        $model = new ViewModel($parameter);
        $model->setTemplate("jobs/error/$script");

        return $model;
    }
}

