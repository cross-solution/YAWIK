<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Applications controllers */
namespace Applications\Controller;

use Applications\Entity\Contact;
use Applications\Listener\Events\ApplicationEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Applications\Entity\Application;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Core\Form\Container;
use Core\Form\SummaryForm;
use Core\Entity\PermissionsInterface;
use Applications\Entity\Status;

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
 * @method \Acl\Controller\Plugin\Acl acl()
 * @method \Core\Controller\Plugin\Notification notification()
 * @method \Core\Controller\Plugin\Mailer mailer()
 * @method \Auth\Controller\Plugin\Auth auth()
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
        $serviceLocator  = $this->serviceLocator;
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
        $services     = $this->serviceLocator;
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $container    = $services->get('forms')->get('Applications/Apply');
        
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
            $user  = $this->auth()->getUser();
            $appId = $this->params('applyId');
            if (!$appId) {
                throw new \RuntimeException('Missing apply id');
            }

            /* @var \Jobs\Entity\Job $job */
            $job = $repositories->get('Jobs/Job')->findOneByApplyId($appId);

            if (!$job) {
                $e->getRouteMatch()->setParam('action', 'job-not-found');
                return;
            }

            switch ($job->getStatus()) {
                case \Jobs\Entity\Status::ACTIVE:
                    break;
                default:
                    $e->getRouteMatch()->setParam('action', 'job-not-found');
                    return;
                    break;
            }

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
                    if ($job->getUriApply()) {
                        return $this->redirect($job->getUriApply());
                    }

                    /* @var $application \Applications\Entity\Application */
                    $application = $repository->create();
                    $application->setIsDraft(true)
                                ->setContact($user->getInfo())
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
                    if ($image = $application->getContact()->getImage()) {
                        $repositories->refresh($image);
                    }
                }
            }
        }
        
        $container->setEntity($application);
        $this->configureContainer($container);
        $this->container = $container;
    }
    
    public function jobNotFoundAction()
    {
        $this->response->setStatusCode(410);
        $model = new ViewModel(
            [ 'content' => /*@translate*/ 'Invalid apply id']
        );
        $model->setTemplate('applications/error/not-found');
        return $model;
    }
    
    public function indexAction()
    {
        /* @var \Applications\Form\Apply $form */
        $form        = $this->container;
        $application = $form->getEntity();
        
        $form->setParam('applicationId', $application->id);

        $model = new ViewModel(
            array(
            'form' => $form,
            'isApplicationValid' => $this->checkApplication($application),
            'application' => $application,
            )
        );
        $model->setTemplate('applications/apply/index');
        return $model;
    }
    
    public function oneClickApplyAction()
    {
        /* @var \Applications\Entity\Application $application */
        $application = $this->container->getEntity();
        $job = $application->getJob();
        $atsMode = $job->getAtsMode();
        
        // check for one click apply
        if (!($atsMode->isIntern() && $atsMode->getOneClickApply()))
        {
            // redirect to regular application
            return $this->redirect()
                ->toRoute('lang/apply', ['applyId' => $job->getApplyId()]);
        }
        
        $network = $this->params('network');

        $hybridAuth = $this->serviceLocator
            ->get('HybridAuthAdapter')
            ->getHybridAuth();
        /* @var $authProfile \Hybrid_User_Profile */
        $authProfile = $hybridAuth->authenticate($network)
           ->getUserProfile();

        /* @var \Auth\Entity\SocialProfiles\AbstractProfile $profile */
        $profile = $this->plugin('Auth/SocialProfiles')->fetch($network);

        $contact = $application->getContact();
        $contact->setEmail($authProfile->emailVerified ?: $authProfile->email);
        $contact->setFirstName($authProfile->firstName);
        $contact->setLastName($authProfile->lastName);
        $contact->setBirthDay($authProfile->birthDay);
        $contact->setBirthMonth($authProfile->birthMonth);
        $contact->setBirthYear($authProfile->birthYear);
        $contact->setPostalCode($authProfile->zip);
        $contact->setCity($authProfile->city);
        $contact->setStreet($authProfile->address);
        $contact->setPhone($authProfile->phone);
        $contact->setGender($authProfile->gender);

        $profiles = $application->getProfiles();
        $profiles->add($profile);

        $cv = $application->getCv();
        $cv->setEmployments($profile->getEmployments());
        $cv->setEducations($profile->getEducations());

        if ($authProfile->photoURL)
        {
            $response = (new \Zend\Http\Client($authProfile->photoURL, ['sslverifypeer' => false]))->send();
            $file = new \Doctrine\MongoDB\GridFSFile();
            $file->setBytes($response->getBody());
            
            $image = new \Applications\Entity\Attachment();
            $image->setName($contact->getLastName().$contact->getFirstName());
            $image->setType($response->getHeaders()->get('Content-Type')->getFieldValue());
            $image->setFile($file);
            $image->setPermissions($application->getPermissions());
            
            $contact->setImage($image);
        }
        
        $urlOptions = [];
        
        if ($this->params('immediately'))
        {
            $application->getAttributes()->setAcceptedPrivacyPolicy(true);
            $urlOptions = [
                'query' => [
                    'do' => 'send'
                ]
            ];
        }
        
        return $this->redirect()
           ->toRoute('lang/apply', ['applyId' => $job->getApplyId()], $urlOptions);
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
            return new JsonModel(
                array(
                'valid' => false,
                'errors' => $form->getMessages(),
                )
            );
        }
        $application = $this->container->getEntity();
        $this->serviceLocator->get('repositories')->store($application);
        
        if ('file-uri' === $this->params()->fromPost('return')) {
            $basepath = $this->serviceLocator->get('ViewHelperManager')->get('basepath');
            $content = $basepath($form->getHydrator()->getLastUploadedFile()->getUri());
        } else {
            if ($form instanceof SummaryForm) {
                $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                $viewHelper = 'summaryform';
            } else {
                $viewHelper = 'form';
            }
            $content = $this->serviceLocator->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
        }
        
        return new JsonModel(
            array(
            'valid' => $form->isValid(),
            'content' => $content,
            'isApplicationValid' => $this->checkApplication($application)
            )
        );
    }
    
    public function doAction()
    {
        $services     = $this->serviceLocator;
        $config       = $services->get('Config');
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        /* @var Application $application*/
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

        if ('previewmail' == $this->params()->fromQuery('do')) {
            $this->mailer('Applications/CarbonCopy', [ 'application' => $application], true);
            $this->notification()->success(/*@translate*/ 'Mail has been send');
            return new JsonModel();
        }

        if ('sendmail' == $this->params()->fromQuery('do')) {
            $jobEntity         = $application->getJob();

            $mailData = array(
                'application' => $application,
                'to'          => $jobEntity->getContactEmail()
            );
            if (array_key_exists('mails', $config) && array_key_exists('from', $config['mails']) && array_key_exists('email', $config['mails']['from'])) {
                $mailData['from'] = $config['mails']['from']['email'];
            }
            $this->mailer('Applications/CarbonCopy', $mailData, true);
            $repositories->remove($application);
            //$this->notification()->success(/*@translate*/ 'Application has been send.');
            $model = new ViewModel(
                array(
                'success' => true,
                'job' => $jobEntity,
                )
            );
            $model->setTemplate('applications/apply/success');
            return $model;
        }

        $application->setIsDraft(false)
            ->setStatus(new Status())
            ->getPermissions()
            ->revoke($this->auth()->getUser(), PermissionsInterface::PERMISSION_CHANGE)
            ->inherit($application->getJob()->getPermissions());

        $events   = $services->get('Applications/Events');
        $events->trigger(ApplicationEvent::EVENT_APPLICATION_POST_CREATE, $this, [ 'application' => $application ]);

        $model = new ViewModel(
            array(
            'success' => true,
            'application' => $application,
            )
        );
        $model->setTemplate('applications/apply/index');

        return $model;
    }

    protected function checkApplication($application)
    {
        return $this->serviceLocator->get('validatormanager')->get('Applications/Application')
                    ->isValid($application);
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

        /** @var $settings \Applications\Entity\Settings */
        $settings = $job->getUser()->getSettings('Applications');
        $formSettings = $settings->getApplyFormSettings();

        if ($formSettings && $formSettings->isActive()) {
            $container->disableElements($formSettings->getDisableElements());
            return;
        }

        $config = $this->serviceLocator->get('Config');
        $config = isset($config['form_elements_config']['Applications/Apply']['disable_elements'])
                ? $config['form_elements_config']['Applications/Apply']['disable_elements']
                : null;
        if ($config) {
            $container->disableElements($config);
        }
    }
}
