<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controller */
namespace Applications\Controller;

use Applications\Form\ApplicationsFilter;
use Applications\Listener\Events\ApplicationEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Applications\Entity\StatusInterface as Status;
use Applications\Entity\Application;

/**
 * @method \Core\Controller\Plugin\Notification notification()
 * @method \Core\Controller\Plugin\Mailer mailer()
 * @method \Acl\Controller\Plugin\Acl acl()
 * @method \Auth\Controller\Plugin\Auth auth()
 *
 * Handles managing actions on applications
 */
class ManageController extends AbstractActionController
{
    /**
     * attaches further Listeners for generating / processing the output
     *
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->serviceLocator;
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $action     = $this->params()->fromQuery('action');
        
        if ($routeMatch && $action) {
            $routeMatch->setParam('action', $action);
        }

        return parent::onDispatch($e);
    }
    
    /**
     * List applications
     */
    public function indexAction()
    {
        return $this->pagination([
                'params' => ['Application_List', ['q', 'job', 'page' => 1, 'unread', 'status' => 'all']],
                'paginator' => ['Applications', 'as' => 'applications'],
                'form' => [
                    ApplicationsFilter::class,
                    'as' => 'form'
                ],
            ]);
    }

    /**
     * Detail view of an application
     *
     * @return array|JsonModel|ViewModel
     */
    public function detailAction()
    {
        if ('refresh-rating' == $this->params()->fromQuery('do')) {
            return $this->refreshRatingAction();
        }
        
        $nav = $this->serviceLocator->get('Core/Navigation');
        $page = $nav->findByRoute('lang/applications');
        $page->setActive();

        /* @var \Applications\Repository\Application$repository */
        $repository = $this->serviceLocator->get('repositories')->get('Applications/Application');
        /* @var Application $application */
        $application = $repository->find($this->params('id'));
        
        if (!$application) {
            $this->response->setStatusCode(410);
            $model = new ViewModel(
                array(
                'content' => /*@translate*/ 'Invalid apply id'
                )
            );
            $model->setTemplate('applications/error/not-found');
            return $model;
        }
        
        $this->acl($application, 'read');
        
        $applicationIsUnread = false;
        if ($application->isUnreadBy($this->auth('id')) && $application->getStatus()) {
            $application->addReadBy($this->auth('id'));
            $applicationIsUnread = true;
            $application->changeStatus(
                $application->getStatus(),
                sprintf(/*@translate*/ 'Application was read by %s',
                                       $this->auth()->getUser()->getInfo()->getDisplayName()));
        }


        
        $format=$this->params()->fromQuery('format');

        if ($application->isDraft()) {
            $list = false;
        } else {
            $list = $this->paginationParams('Applications\Index', $repository);
            $list->setCurrent($application->getId());
        }

        $return = array(
            'application'=> $application,
            'list' => $list,
            'isUnread' => $applicationIsUnread,
            'format' => 'html'
        );
        switch ($format) {
            case 'json':
                /*@deprecated - must be refactored */
                        $viewModel = new JsonModel();
                        $viewModel->setVariables(
                            /*array(
                            'application' => */$this->serviceLocator
                                              ->get('builders')
                                              ->get('JsonApplication')
                                              ->unbuild($application)
                        );
                        $viewModel->setVariable('isUnread', $applicationIsUnread);
                        $return = $viewModel;
                break;
            case 'pdf':
                $pdf = $this->serviceLocator->get('Core/html2pdf');
                $return['format'] = $format;
                break;
            default:
                $contentCollector = $this->getPluginManager()->get('Core/ContentCollector');
                $contentCollector->setTemplate('applications/manage/details/action-buttons');
                $actionButtons = $contentCollector->trigger('application.detail.actionbuttons', $application);
                
                $return = new ViewModel($return);
                $return->addChild($actionButtons, 'externActionButtons');
                
                $allowSubsequentAttachmentUpload = $this->serviceLocator->get('Applications/Options')
                    ->getAllowSubsequentAttachmentUpload();
                
                if ($allowSubsequentAttachmentUpload
                    && $this->acl($application, Application::PERMISSION_SUBSEQUENT_ATTACHMENT_UPLOAD, 'test')
                ) {
                    $attachmentsForm = $this->serviceLocator->get('forms')
                        ->get('Applications/Attachments');
                    $attachmentsForm->bind($application->getAttachments());
                    
                    /* @var $request \Zend\Http\PhpEnvironment\Request */
                    $request = $this->getRequest();
                    
                    if ($request->isPost() && $attachmentsForm->get('return')->getValue() === $request->getPost('return')) {
                        $data = array_merge(
                            $attachmentsForm->getOption('use_post_array') ? $request->getPost()->toArray() : [],
                            $attachmentsForm->getOption('use_files_array') ? $request->getFiles()->toArray() : []
                        );
                        $attachmentsForm->setData($data);
                        
                        if (!$attachmentsForm->isValid()) {
                            return new JsonModel([
                                'valid' => false,
                                'errors' => $attachmentsForm->getMessages()
                            ]);
                        }
                        
                        $content = $attachmentsForm->getHydrator()
                            ->getLastUploadedFile()
                            ->getUri();
                        
                        return new JsonModel([
                            'valid' => $attachmentsForm->isValid(),
                            'content' => $content
                        ]);
                    }
                    
                    $return->setVariable('attachmentsForm', $attachmentsForm);
                }
                
                break;
        }
        
        return $return;
    }
    
    /**
     * Refreshes the rating of an application
     *
     * @throws \DomainException
     * @return \Zend\View\Model\ViewModel
     */
    public function refreshRatingAction()
    {
        $model = new ViewModel();
        $model->setTemplate('applications/manage/_rating');
        
        $application = $this->serviceLocator->get('repositories')->get('Applications/Application')
                        ->find($this->params('id', 0));
        
        if (!$application) {
            throw new \DomainException('Invalid application id.');
        }
        
        $model->setVariable('application', $application);
        return $model;
    }
    
    /**
     * Attaches a social profile to an application
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function socialProfileAction()
    {
        if ($spId = $this->params()->fromQuery('spId')) {
            $repositories = $this->serviceLocator->get('repositories');
            $repo = $repositories->get('Applications/Application');
            $profile = $repo->findProfile($this->params()->fromQuery('spId'));
            if (!$profile) {
                throw new \InvalidArgumentException('Could not find profile.');
            }
        } elseif ($this->getRequest()->isPost()
                   && ($network = $this->params()->fromQuery('network'))
                   && ($data    = $this->params()->fromPost('data'))
        ) {
            $profileClass = '\\Auth\\Entity\\SocialProfiles\\' . $network;
            $profile      = new $profileClass();
            $profile->setData(\Zend\Json\Json::decode($data, \Zend\Json\Json::TYPE_ARRAY));
        } else {
            throw new \RuntimeException(
                'Missing arguments. Either provide "spId" as Get or "network" and "data" as Post.'
            );
        }
        
        return array(
            'profile' => $profile
        );
    }

    /**
     * Changes the status of an application
     *
     * @return array
     */
    public function statusAction()
    {
        $applicationId = $this->params('id');
        /* @var \Applications\Repository\Application $repository */
        $repository    = $this->serviceLocator->get('repositories')->get('Applications/Application');
        /* @var Application $application */
        $application   = $repository->find($applicationId);

        /* @var Request $request */
        $request = $this->getRequest();

        if (!$application) {
            throw new \InvalidArgumentException('Could not find application.');
        }
        
        $this->acl($application, 'change');
        
        $jsonFormat    = 'json' == $this->params()->fromQuery('format');
        $status        = $this->params('status', Status::CONFIRMED);
        $settings = $this->settings();
        
        if (in_array($status, array(Status::INCOMING))) {
            $application->changeStatus($status);
            if ($request->isXmlHttpRequest()) {
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

        $events = $this->serviceLocator->get('Applications/Events');

        /* @var ApplicationEvent $event */
        $event = $events->getEvent(ApplicationEvent::EVENT_APPLICATION_STATUS_CHANGE,
                                   $this,
                                   [
                                       'application' => $application,
                                       'status' => $status,
                                       'user' => $this->auth()->getUser(),
                                   ]
        );

        $event->setIsPostRequest($request->isPost());
        $event->setPostData($request->getPost());
        $events->trigger($event);

        $params = $event->getFormData();


        if ($request->isPost()) {

            if ($jsonFormat) {
                return array(
                    'status' => 'success',
                );
            }
            $this->notification()->success($event->getNotification());
            return $this->redirect()->toRoute('lang/applications/detail', array(), true);
        }

        if ($jsonFormat) {
            return $params;
        }

        /* @var \Applications\Form\Mail $form */
        $form = $this->serviceLocator->get('FormElementManager')->get('Applications/Mail');
        $form->populateValues($params);



        $reciptient = $params['to'];

        return [
            'recipient' => $reciptient,
            'form' => $form
        ];
    }
    
    /**
     * Forwards an application via Email
     *
     * @throws \InvalidArgumentException
     * @return \Zend\View\Model\JsonModel
     */
    public function forwardAction()
    {
        $services     = $this->serviceLocator;
        $emailAddress = $this->params()->fromQuery('email');
        /* @var \Applications\Entity\Application $application */
        $application  = $services->get('repositories')->get('Applications/Application')
                                 ->find($this->params('id'));
        
        $this->acl($application, 'forward');
        
        $translator   = $services->get('translator');
         
        if (!$emailAddress) {
            throw new \InvalidArgumentException('An email address must be supplied.');
        }
        
        $params = array(
            'ok' => true,
            'text' => sprintf($translator->translate('Forwarded application to %s'), $emailAddress)
        );
        
        try {
            $userName    = $this->auth('info')->getDisplayName();
            $fromAddress = $application->getJob()->getContactEmail();
            $mailOptions = array(
                'application' => $application,
                'to'          => $emailAddress,
                'from'        => array($fromAddress => $userName)
            );
            $this->mailer('Applications/Forward', $mailOptions, true);
            $this->notification()->success($params['text']);
        } catch (\Exception $ex) {
            $params = array(
                'ok' => false,
                'text' => sprintf($translator->translate('Forward application to %s failed.'), $emailAddress)
            );
            $this->notification()->error($params['text']);
        }
        $application->changeStatus($application->getStatus(), $params['text']);
        return new JsonModel($params);
    }

    /**
     * Deletes an application
     *
     * @return array|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $id          = $this->params('id');
        $services    = $this->serviceLocator;
        $repositories= $services->get('repositories');
        $repository  = $repositories->get('Applications/Application');
        $application = $repository->find($id);
        
        if (!$application) {
            throw new \DomainException('Application not found.');
        }

        $this->acl($application, 'delete');

        $events   = $services->get('Applications/Events');
        $events->trigger(ApplicationEvent::EVENT_APPLICATION_PRE_DELETE, $this, [ 'application' => $application ]);
        
        $repositories->remove($application);
        
        if ('json' == $this->params()->fromQuery('format')) {
            return ['status' => 'success'];
        }
        
        return $this->redirect()->toRoute('lang/applications', array(), true);
    }

    /**
     * Move an application to talent pool
     *
     * @return \Zend\Http\Response
     * @since 0.26
     */
    public function moveAction()
    {
        $id = $this->params('id');
        $serviceManager = $this->serviceLocator;
        $repositories = $serviceManager->get('repositories');
        $application = $repositories->get('Applications/Application')->find($id);
        
        if (!$application) {
            throw new \DomainException('Application not found.');
        }

        $this->acl($application, 'move');
        
        $user = $this->auth()->getUser();
        $cv = $repositories->get('Cv/Cv')->createFromApplication($application, $user);
        
        $repositories->store($cv);
        $repositories->remove($application);

        $this->notification()->success(
            /*@translate*/ 'Application has been successfully moved to Talent Pool');
        
        return $this->redirect()->toRoute('lang/applications', array(), true);
    }
}
