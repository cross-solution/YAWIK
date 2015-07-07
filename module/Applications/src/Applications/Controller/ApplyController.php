<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controllers */ 
namespace Applications\Controller;

use Applications\Entity\Contact;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Applications\Entity\Application;
use Zend\View\Model\ViewModel;
use Auth\Entity\AnonymousUser;
use Zend\View\Model\JsonModel;
use Core\Form\Container;
use Core\Form\SummaryForm;
use Core\Entity\PermissionsInterface;
use Applications\Entity\Status;
use Applications\Entity\StatusInterface;

/**
 * there are basically two ways to use this controller,
 * (1) either you have a form, and want to accumulate inputs, or you want to create a form on an existing or new application
 * (2) or want to do some action on a concrete form.
 *
 * for both you need the applyId, which is NOT the application-id, the applyId is an alias for the Job, it can be some human-readable text or an external ID.
 * the application to an applyId is found by the combination of the user and the job, that is represented by the applyId.
 * this approach ensures, that applications stick to the related job.
 *
 * nonetheless, there is an exception, for the posts for updating the application, the application-id is needed.
 *
 * if you use the do as query-parameter, you have to customize the do-Action for the special purpose that is assigned to the do parameter in the query
 *
 * @method \Auth\Controller\Plugin\Auth auth
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ApplyController extends AbstractActionController
{
    
    protected $container;
    
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events->attach($defaultServices);
        return $this;
    }
    
    public function preDispatch(MvcEvent $e)
    {
        /* @var $application \Applications\Entity\Application */
        if ($this->params()->fromQuery('do')) {
            $e->getRouteMatch()->setParam('action', 'do');
            return;
        }

        /* @var $request    \Zend\Http\Request */
        /* @var $repository \Applications\Repository\Application */
        /* @var $container  \Applications\Form\Apply */
        $request      = $this->getRequest();
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $container    = $services->get('forms')->get('Applications/Apply');
        $user  = $this->auth()->getUser();

        if ($request->isPost()) {
            $appId = $this->params()->fromPost('applicationId');
            if (!$appId) {
                throw new \RuntimeException('Missing application id.');
            }
            $routeMatch = $e->getRouteMatch();

            if ('recruiter-preview' == $appId) {
                $routeMatch->setParam('action', 'process-preview');
                return;
            }

            $application = $repository->find($appId);
            if (!$application) {
                throw new \RuntimeException('Invalid application id.');
            }
            $action     = 'process';



            $routeMatch->setParam('action', $action);
            
        } else {

            $appId = $this->params('applyId');
            if (!$appId) {
                throw new \RuntimeException('Missing apply id');
            }

            $job = $repositories->get('Jobs/Job')->findOneByApplyId($appId);

            if ($user === $job->getUser()) {
                $application = new \Applications\Entity\Application();
                $application->setContact(new Contact());
                $application->setJob($job);
                $application->setId('recruiter-preview');
            } else {

                $subscriberUri = $this->params()->fromQuery('subscriber');
                $application   = $repository->findDraft($user, $appId);

                if ($application) {
                    /* @var $form \Auth\Form\UserInfo */
                    $form = $container->getForm('contact.contact');
                    $form->setDisplayMode('summary');

                    if ($subscriberUri) {
                        $subscriber = $application->getSubscriber();
                        if (!$subscriber || $subscriber->uri != $subscriberUri) {
                            $subscriber = $repositories->get('Applications/Subscriber')->findbyUri($subscriberUri, /*create*/ true);
                            $application->setSubscriber($subscriber);
                            $subscriber->getname();
                        }
                    }

                } else {

                    if (!$job) {
                        $e->getRouteMatch()->setParam('action', 'job-not-found');
                        return;
                    }

                    /* @var $application \Applications\Entity\Application */
                    $application = $repository->create();
                    $application->setIsDraft(true)
                                ->setContact($user->info)
                                ->setUser($user)
                                ->setJob($job);

                    if ($subscriberUri) {
                        $subscriber = $repositories->get('Applications/Subscriber')->findbyUri($subscriberUri, /*create*/ true);
                        $application->setSubscriber($subscriber);
                    }

                    $repositories->store($application);
                    /*
                     * If we had copy an user image, we need to refresh its data
                     * to populate the length property.
                     */
                    if ($image = $application->contact->image) {
                        $repositories->refresh($image);
                    }
                }
            }
        }
        // set anonymous user, if none is set.
        if (null === $application->getUser() && $user instanceOf AnonymousUser) {
            $application->setUser($user);
        }
        $container->setEntity($application);
        $this->configureContainer($container);
        $this->container = $container;
    }
    
    public function jobNotFoundAction()
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
        $form        = $this->container;
        $application = $form->getEntity();

        $this->container->setParam('applicationId', $application->id);

        $model = new ViewModel(array(
            'form' => $form,
            'isApplicationValid' => $this->checkApplication($application),
            'application' => $application,
            'hasApplied' => $this->checkHasApplied($application),
        ));

        $model->setTemplate('applications/apply/index');
        return $model;

    }

    public function processPreviewAction()
    {
        return new JsonModel(array('valid' => false, 'errors' => array()));
    }
    
    public function processAction()
    {
        $formName  = $this->params()->fromQuery('form');
        $form      = $this->container->getForm($formName);
        $postData  = $form->getOption('use_post_array') ? $_POST : array();
        $filesData = $form->getOption('use_files_array') ? $_FILES : array();
        $data      = array_merge($postData, $filesData);

        $form->setData($data);
        
        if (!$form->isValid()) {
            return new JsonModel(array(
                'valid' => false,
                'errors' => $form->getMessages(),
            ));
        }
        $application = $this->container->getEntity();
        $this->getServiceLocator()->get('repositories')->store($application);
        
        if ('file-uri' === $this->params()->fromPost('return')) {
            $basepath = $this->getServiceLocator()->get('ViewHelperManager')->get('basepath');
            $content = $basepath($form->getHydrator()->getLastUploadedFile()->getUri());
        } else {
            if ($form instanceOf SummaryForm) {
                $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                $viewHelper = 'summaryform';
            } else {
                $viewHelper = 'form';
            }
            $content = $this->getServiceLocator()->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
        }
        
        return new JsonModel(array(
            'valid' => $form->isValid(),
            'content' => $content,
            'isApplicationValid' => $this->checkApplication($application),
            'hasApplied' => $this->checkHasApplied($application),
        ));
    }
    
    public function doAction()
    {
        $services     = $this->getServiceLocator();
        $config       = $services->get('Config');
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $application  = $repository->findDraft(
                            $this->auth()->getUser(),
                            $this->params('applyId')
                        );
        
        if (!$application) {
            throw new \Exception('No application draft found.');
        }
        
        if ('abort' == $this->params()->fromQuery('do')) {
            $repositories->remove($application);
            return $this->redirect()->toRoute('lang/apply', array('applyId' => $this->params('applyId')));
        }
        
        if (!$this->checkApplication($application)) {
            $this->notification()->error(/*@translate*/ 'There are missing required informations. Your application cannot be send.');
            return $this->redirect()->toRoute('lang/apply', array('applyId' => $this->params('applyId')));
        }

        if ('sendmail' == $this->params()->fromQuery('do')) {
            $jobEntity         = $application->job;;
            $mailData = array(
                'application' => $application,
                'to'          => $jobEntity->contactEmail
            );
            if (array_key_exists('mails', $config) && array_key_exists('from', $config['mails']) && array_key_exists('email', $config['mails']['from'])) {
                $mailData['from'] = $config['mails']['from']['email'];
            }
            $this->mailer('Applications/CarbonCopy', $mailData, TRUE);
            $repositories->remove($application);
            //$this->notification()->success(/*@translate*/ 'Application has been send.');
            $model = new ViewModel(array(
                'success' => true,
                'job' => $jobEntity,
            ));
            $model->setTemplate('applications/apply/success');
            return $model;
        }

        $application->setIsDraft(false)
                    ->setStatus(new Status())
                    ->getPermissions()
                        ->revoke($this->auth()->getUser(), PermissionsInterface::PERMISSION_CHANGE)
                        ->inherit($application->getJob()->getPermissions());

        $this->sendRecruiterMails($application);
        $this->sendUserMails($application);

        $model = new ViewModel(array(
            'success' => true,
            'application' => $application,
        ));
        $model->setTemplate('applications/apply/index');

        return $model;
    }
    
    
    
    protected function checkApplication($application)
    {
        return $this->getServiceLocator()->get('validatormanager')->get('Applications/Application')
                    ->isValid($application);
    }

    protected function checkHasApplied($application)
    {
        $repository = $this->getServiceLocator()
                           ->get('Applications/Repository/HasApplied');

        return $repository->hasApplied($application);

    }
    
    protected function sendRecruiterMails($application)
    {
        $job = $application->getJob();
        $recruiter = $this->getServiceLocator()
                          ->get('repositories')
                          ->get('Auth/User')->findOneByEmail($job->contactEmail);
        
        if (!$recruiter) {
            $recruiter = $job->user;
            $admin     = false;
        } else {
            $admin     = $job->user;
        }
        
        $settings = $recruiter->getSettings('Applications');
        if ($settings->getMailAccess()) {
            $this->mailer('Applications/NewApplication', array('job' => $job, 'user' => $recruiter, 'admin' => $admin), /*send*/ true);
        }
        if ($settings->getAutoConfirmMail()) {
            $ackBody = $settings->getMailConfirmationText();
            if (empty($ackBody)) {
                $ackBody = $job->user->getSettings('Applications')->getMailConfirmationText();
            }
            if (!empty($ackBody)) {
        
                /* Acknowledge mail to applier */
                $ackMail = $this->mailer('Applications/Confirmation',
                    array('application' => $application,
                        'body' => $ackBody,
                    ));
                // Must be called after initializers in creation
                $ackMail->setSubject(/*@translate*/ 'Application confirmation');
                $ackMail->setFrom($recruiter->getInfo()->getEmail());
                $this->mailer($ackMail);
                $application->changeStatus(StatusInterface::CONFIRMED, sprintf('Mail was sent to %s' , $application->contact->email));
            }
        }
        
    }
    
    protected function sendUserMails($application)
    {
        if ($application->getAttributes()->getSendCarbonCopy()) {
            $this->mailer('Applications/CarbonCopy', array(
                    'application' => $application,
                ), /*send*/ true);
        }
    }

    /**
     * Configures the apply form container.
     *
     * Currently only disables elements.
     *
     * @param Container $container
     */
    protected function configureContainer(Container $container)
    {
        /* @var $application Application */
        $application = $container->getEntity();
        $job         = $application->getJob();

        /*
         * @TODO: Implement disable elements logic in entities, etc.
         *


        $config = $job->getApplyFormElementsConfig();
        if ($config) {
            $container->disableElements($config);
            return;
        }

        $config = $job->getOrganization()->getApplyFormElementsConfig();
        if ($config) {
            $container->disableElements($config);
            return;
        }
        */

        /** @var $settings \Applications\Entity\Settings */
        $settings = $job->getUser()->getSettings('Applications');
        $formSettings = $settings->getApplyFormSettings();

        if ($formSettings && $formSettings->isActive()) {
            $container->disableElements($formSettings->getDisableElements());
            return;
        }

        $config = $this->getServiceLocator()->get('Config');
        $config = isset($config['form_elements_config']['Applications/Apply']['disable_elements'])
                ? $config['form_elements_config']['Applications/Apply']['disable_elements']
                : null;
        if ($config) {
            $container->disableElements($config);
        }
    }
}
