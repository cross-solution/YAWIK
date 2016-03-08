<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Jobs\Entity\Status;
use Core\Repository\RepositoryService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Core\Form\SummaryForm;
use Zend\Mvc\MvcEvent;
use Jobs\Listener\Events\JobEvent;
use Core\Form\SummaryFormInterface;
use Zend\Stdlib\ArrayUtils;
use Auth\AuthenticationService;
use Zend\Mvc\I18n\Translator;
use Zend\Http\PhpEnvironment\Response;

/**
 * This Controller handles management actions for jobs.
 *
 * @method \Acl\Controller\Plugin\Acl acl()
 * @method \Jobs\Controller\Plugin\InitializeJob initializeJob()
 * @method \Core\Controller\Plugin\Notification notification()
 * @method \Core\Controller\Plugin\EntitySnapshot entitySnapshot()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ManageController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var RepositoryService
     */
    protected $repositoryService;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param AuthenticationService $auth
     * @param RepositoryService     $repositoryService
     * @param                       $translator
     */
    public function __construct(AuthenticationService $auth, RepositoryService $repositoryService, Translator $translator) {
        $this->auth = $auth;
        $this->repositoryService = $repositoryService;
        $this->translator = $translator;
    }

    /**
     * @return $this|void
     */
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
     * Attaches the MailSender aggregate listener to the job event manager.
     *
     * @param MvcEvent $e
     * @since 0.19
     */
    public function preDispatch(MvcEvent $e)
    {
        if ('calculate' == $this->params()->fromQuery('do')) {
            return;
        }
        $routeMatch = $e->getRouteMatch();
        $action = $routeMatch->getParam('action');

        if (in_array($action, array('edit', 'approval', 'completion'))) {
            $services = $this->getServiceLocator();
            $jobEvents = $services->get('Jobs/Events');
            $mailSender = $services->get('Jobs/Listener/MailSender');

            $mailSender->attach($jobEvents);
        }
    }

    /**
     * @TODO edit-Action and save-Action are doing the same, one of them has to quit
     *
     * @return null|ViewModel
     */
    public function editAction()
    {
        if ('calculate' == $this->params()->fromQuery('do')) {
            $calc = $this->getServiceLocator()->get('filtermanager')->get('Jobs/ChannelPrices');
            $sum = $calc->filter($this->params()->fromPost('channels'));

            return new JsonModel(['sum' => $sum]);
        }
        return $this->save();
    }

    /**
     * @return null|ViewModel
     */
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
        $user               = $this->auth->getUser();
        if (empty($user->info->email)) {
            return $this->getErrorViewModel('no-parent', array('cause' => 'noEmail'));
        }
        $userOrg            = $user->getOrganization();
        if (!$userOrg->hasAssociation()) {
            return $this->getErrorViewModel('no-parent', array('cause' => 'noCompany'));
        }

        /** @var \Zend\Http\Request $request */
        $request            = $this->getRequest();
        $isAjax             = $request->isXmlHttpRequest();
        $pageToForm         = array(0 => array('locationForm', 'nameForm', 'portalForm'),
                                    1 => array('descriptionForm'),
                                    2 => array('previewForm'));

        $params             = $this->params();
        $formIdentifier     = $params->fromQuery('form');
        $pageIdentifier     = (int) $params->fromQuery('page', array_key_exists('page', $parameter)?$parameter['page']:0);

        $jobEntity = $this->initializeJob()->get($this->params(), true);

        $viewModel          = null;
        $this->acl($jobEntity, 'edit');
        $form               = $this->getFormular($jobEntity);

        $valid              = true;
        $instanceForm       = null;
        $formErrorMessages = array();

        if (isset($formIdentifier) &&  $request->isPost()) {
            // at this point the form get instantiated and immediately accumulated
            $instanceForm = $form->getForm($formIdentifier);
            if (!isset($instanceForm)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            // the id may be part of the postData, but it never should be altered
            $postData = $request->getPost();
            if (isset($postData['id'])) {
                unset($postData['id']);
            }
            unset($postData['applyId']);
            $instanceForm->setData($postData);
            $valid = $instanceForm->isValid();
            $formErrorMessages = ArrayUtils::merge($formErrorMessages, $instanceForm->getMessages());
            if ($valid) {
                /*
                 * @todo This is a workaround for GeoJSON data insertion
                 * until we figured out, what we really want it to be.
                 */
                if ('locationForm' == $formIdentifier) {
                    $locElem = $instanceForm->getBaseFieldset()->get('geo-location');
                    if ($locElem instanceOf \Geo\Form\GeoText) {
                        $loc = $locElem->getValue('entity');
                        $locations = $jobEntity->getLocations();
                        if (count($locations)) { $locations->clear(); }
                        $locations->add($loc);
                        $jobEntity->setLocation($locElem->getValue());
                    }
                }

                $title = $jobEntity->getTitle();
                $templateTitle = $jobEntity->getTemplateValues()->getTitle();

                if (empty($templateTitle)) {
                    $jobEntity->getTemplateValues()->setTitle($title);
                }
                $this->repositoryService->persist($jobEntity);
            }
        }

        // validation
        $jobValid = true;
        $errorMessage = array();
        if (empty($jobEntity->getTitle())) {
            $jobValid = false;
            $errorMessage[] = $this->translator->translate('No Title');
        }
        if (empty($jobEntity->getLocation())) {
            $jobValid = false;
            $errorMessage[] = $this->translator->translate('No Location');
        }
        if (empty($jobEntity->getTermsAccepted())) {
            $jobValid = false;
            $errorMessage[] = $this->translator->translate('Accept the Terms');
        }

        $errorMessage = '<br />' . implode('<br />', $errorMessage);
        if ($isAjax) {
            if ($instanceForm instanceof SummaryForm) {
                $instanceForm->setRenderMode(SummaryForm::RENDER_SUMMARY);
                $viewHelper = 'summaryform';
            } else {
                $viewHelper = 'form';
            }
            $viewHelperManager  = $serviceLocator->get('ViewHelperManager');
            $content = $viewHelperManager->get($viewHelper)->__invoke($instanceForm);
            $viewModel = new JsonModel(
                array(
                'content' => $content,
                'valid' => $valid,
                'jobvalid' => $jobValid,
                'errors' => $formErrorMessages,
                'errorMessage' => $errorMessage)
            );
        } else {
            if (isset($pageIdentifier)) {
                $form->disableForm();
                if (array_key_exists($pageIdentifier, $pageToForm)) {
                    foreach ($pageToForm[$pageIdentifier] as $actualFormIdentifier) {
                        $form->enableForm($actualFormIdentifier);
                        if ($jobEntity->isDraft()) {
                            $actualForm = $form->get($actualFormIdentifier);
                            if ('nameForm' != $actualFormIdentifier && $actualForm instanceof SummaryFormInterface) {
                                $form->get($actualFormIdentifier)->setDisplayMode(SummaryFormInterface::DISPLAY_FORM);
                            }
                            if ('locationForm' == $actualFormIdentifier) {
                                $locElem = $actualForm->getBaseFieldset()->get('geo-location');
                                if ($locElem instanceOf \Geo\Form\GeoText) {
                                    $loc = $jobEntity->getLocations();
                                    if (count($loc)) {
                                        $locElem->setValue($loc->first());
                                    }
                                }
                            }
                        }
                    }
                    if (!$jobEntity->isDraft()) {
                        // Job is deployed, some changes are now disabled
                        $form->enableAll();
                    }
                } else {
                    throw new \RuntimeException('No form found for page ' . $pageIdentifier);
                }
            }
            $pageLinkNext = null;
            $pageLinkPrevious = null;
            if (0 < $pageIdentifier) {
                $pageLinkPrevious = $this->url()->fromRoute(
                    'lang/jobs/manage',
                    [],
                    ['query' => [
                        'id' => $jobEntity->getId(),
                        'page' => $pageIdentifier - 1]
                    ]
                );
            }
            if ($pageIdentifier < count($pageToForm) - 1) {
                $pageLinkNext     = $this->url()->fromRoute(
                    'lang/jobs/manage',
                    [],
                    [
                        'query' => [
                            'id' => $jobEntity->getId(),
                            'page' => $pageIdentifier + 1]
                    ]
                );
            }
            $completionLink = $this->url()->fromRoute(
                'lang/jobs/completion',
                [ 'id' => $jobEntity->getId()]
            );

            $viewModel = $this->getViewModel($form);

            $viewModel->setVariables(
                array(
                'pageLinkPrevious' => $pageLinkPrevious,
                'pageLinkNext' => $pageLinkNext,
                'completionLink' => $completionLink,
                'page' => $pageIdentifier,
                'title' => $jobEntity->getTitle(),
                'job' => $jobEntity,
                'summary' => 'this is what we charge you for your offer...',
                'valid' => $valid,
                'jobvalid' => $jobValid,
                'errorMessage' => $errorMessage,
                'isDraft' => $jobEntity->isDraft()
                )
            );
        }
        return $viewModel;
    }

    /**
     * @return array
     */
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

    /**
     * @param $job
     * @return mixed
     */
    protected function getFormular($job)
    {
        $services = $this->getServiceLocator();
        /* @var $forms \Zend\Form\FormElementManager */
        $forms    = $services->get('FormElementManager');
        /* @var $container \Jobs\Form\Job */

        $container = $forms->get(
            'Jobs/Job',
            array(
            'mode' => $job->id ? 'edit' : 'new'
            )
        );
        $container->setEntity($job);
        $container->setParam('job', $job->id);
        $container->setParam('applyId', $job->applyId);
        return $container;
    }

    /**
     * @param $form
     * @param array $params
     * @return ViewModel
     */
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
     * Job opening is completed.
     *
     * @return array
     */
    public function completionAction()
    {
        $serviceLocator = $this->getServiceLocator();

        $job = $this->initializeJob()->get($this->params());

        $job->setIsDraft(false);

        $reference = $job->getReference();

        if (empty($reference)) {
            /* @var $repository \Jobs\Repository\Job */
            $repository = $this->repositoryService->get('Jobs/Job');
            $job->setReference($repository->getUniqueReference());
        }
        $job->changeStatus(Status::CREATED, "job was created");
        $job->setAtsEnabled(true);

        // sets ATS-Mode on intern
        $job->getAtsMode();

        /*
         * make the job opening persist and fire the EVENT_JOB_CREATED
         */
        $this->repositoryService->store($job);

        $jobEvents = $serviceLocator->get('Jobs/Events');
        $jobEvents->trigger(JobEvent::EVENT_JOB_CREATED, $this, array('job' => $job));

        return array('job' => $job);
    }

    /**
     * all actions around approve or decline jobs-offers
     *
     * @return array with the viewVariables
     */
    public function approvalAction()
    {
        $serviceLocator = $this->getServiceLocator();
        $user           = $this->auth->getUser();

        $params         = $this->params('state');

        $jobEntity = $this->initializeJob()->get($this->params());
        $jobEvent       = $serviceLocator->get('Jobs/Event');
        $jobEvent->setJobEntity($jobEntity);
        $jobEvent->addPortal('XingVendorApi');
        $jobEvents      = $serviceLocator->get('Jobs/Events');
        // array with differences between the last snapshot and the actual entity
        // is remains Null if there is no snapshot
        // it will be an empty array if the snapshot and the actual entity do not differ
        $diff           = null;
        // preliminary difference, contain all differences
        $prelDiff = $this->entitySnapshot()->diff($jobEntity);
        if (isset($prelDiff)) {
            // we want just some Values to be compared
            $diff = null;
            foreach (array('title', 'organization', 'location',
                         'templateValues.qualifications', 'templateValues.requirements', 'templateValues.benefits', 'templateValues.title',
                         'templateValues._freeValues.description',
                     ) as $prelKey) {
                if (array_key_exists($prelKey, $prelDiff)) {
                    $diff[$prelKey] = $prelDiff[$prelKey];
                }
            }
        }

        if ($params == 'declined') {
            $jobEntity->changeStatus(Status::REJECTED, sprintf(/*@translate*/ "Job opening was rejected by %s", $user->getInfo()->getDisplayName()));
            $jobEntity->setIsDraft(true);
            $this->repositoryService->store($jobEntity);
            $jobEvents->trigger(JobEvent::EVENT_JOB_REJECTED, $jobEvent);
            $this->notification()->success(/*@translate */'Job has been rejected');
        }

        if ($params == 'approved') {
            $jobEntity->changeStatus(Status::ACTIVE, sprintf(/*@translate*/ "Job opening was activated by %s", $user->getInfo()->getDisplayName()));
            $jobEntity->setDatePublishStart();
            $this->repositoryService->store($jobEntity);
            $jobEvents->trigger(JobEvent::EVENT_JOB_ACCEPTED, $jobEvent);
            $this->entitySnapshot($jobEntity);
            $this->notification()->success(/* @translate */ 'Job has been approved');
            return $this->redirect()->toRoute('lang/jobs/listOpenJobs', array(), true);
        }

        $viewLink = $this->url()->fromRoute(
            'lang/jobs/view',
            array(),
            array('query' =>
                      array( 'id' => $jobEntity->getId()))
        );

        $approvalLink = $this->url()->fromRoute(
            'lang/jobs/approval',
            array('state' => 'approved'),
            array('query' =>
                      array( 'id' => $jobEntity->getId()))
        );

        $declineLink = $this->url()->fromRoute(
            'lang/jobs/approval',
            array('state' => 'declined'),
            array('query' =>
                      array( 'id' => $jobEntity->getId()))
        );

        return array('job' => $jobEntity,
                     'diffSnapshot' => $diff,
                     'viewLink' => $viewLink,
                     'approvalLink' => $approvalLink,
                     'declineLink' => $declineLink);
    }

    /**
     * Deactivate a job posting
     *
     * @return null|ViewModel
     */
    public function deactivateAction()
    {
        $user           = $this->auth->getUser();

        $jobEntity = $this->initializeJob()->get($this->params());

        try {
            $jobEntity->changeStatus(Status::INACTIVE, sprintf(/*@translate*/ "Job was deactivated by %s", $user->getInfo()->getDisplayName()));
            $this->notification()->success(/*@translate*/ 'Job has been deactivated');
        } catch (\Exception $e) {
            $this->notification()->danger(/*@translate*/ 'Job could not be deactivated');
        }
        return $this->save(array('page' => 2));
    }

    /**
     * Assign a template to a job posting
     *
     * @return JsonModel
     */
    public function templateAction()
    {
        try {
            $jobEntity = $this->initializeJob()->get($this->params());
            $jobEntity->setTemplate($this->params('template', 'default'));
            $this->repositoryService->store($jobEntity);
            $this->notification()->success(/* @translate*/ 'Template changed');
        } catch (\Exception $e) {
            $this->notification()->danger(/* @translate */ 'Template not changed');
        }

        return new JsonModel(array());
    }

    /**
     * @param $script
     * @param array $parameter
     * @return ViewModel
     */
    protected function getErrorViewModel($script, $parameter = array())
    {
        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_500);

        $model = new ViewModel($parameter);
        $model->setTemplate("jobs/error/$script");

        return $model;
    }

    public function historyAction()
    {
        $jobEntity = $this->initializeJob()->get($this->params());
        $title          = $jobEntity->getTitle();
        $historyEntity  = $jobEntity->getHistory();

        $model = new ViewModel(array('title' => $title, 'history' => $historyEntity));
        $model->setTerminal(true);
        return $model;
    }
}
