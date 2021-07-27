<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** Applications controllers */
namespace Applications\Controller;

use Acl\Controller\Plugin\Acl;
use Applications\Entity\Attachment;
use Applications\Entity\Contact;
use Applications\Entity\Settings;
use Applications\Form\Apply;
use Applications\Listener\Events\ApplicationEvent;
use Applications\Service\UploadHandler;
use Auth\Controller\Plugin\Auth;
use Auth\Entity\SocialProfiles\AbstractProfile;
use Auth\Form\UserInfo;
use Core\Controller\Plugin\Mailer;
use Core\Controller\Plugin\Notification;
use Core\EventManager\EventManager;
use Core\Factory\ContainerAwareInterface;
use Core\Repository\RepositoryService;
use Doctrine\MongoDB\GridFSFile;
use Exception;
use Hybrid_User_Profile;
use Interop\Container\ContainerInterface;
use Jobs\Repository\Job;
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Applications\Entity\Application;
use Laminas\Validator\ValidatorPluginManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Core\Form\Container;
use Core\Form\SummaryForm;
use Core\Entity\PermissionsInterface;
use Applications\Entity\Status;
use Organizations\ImageFileCache\Manager as OrganizationImageCacheManager;
use RuntimeException;

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
 * @method Acl acl()
 * @method Notification notification()
 * @method Mailer mailer()
 * @method Auth auth()
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ApplyController extends AbstractActionController
{

    protected $formContainer;

    protected array $config;

    protected OrganizationImageCacheManager $imageCacheManager;

    protected ValidatorPluginManager $validator;

    protected RepositoryService $repositories;

    protected EventManager $appEvents;

    protected HelperPluginManager $viewHelper;

    /**
     * @var UploadHandler
     */
    private UploadHandler $uploadHandler;

    public function __construct(
        OrganizationImageCacheManager $imageCacheManager,
        ValidatorPluginManager $validator,
        RepositoryService $repositories,
        EventManager $appEvents,
        HelperPluginManager $viewHelper,
        UploadHandler $uploadHandler,
        array $config
    )
    {
        $this->imageCacheManager = $imageCacheManager;
        $this->validator = $validator;
        $this->repositories = $repositories;
        $this->appEvents = $appEvents;
        $this->viewHelper = $viewHelper;
        $this->config = $config;
        $this->uploadHandler = $uploadHandler;
    }

	/**
	 * @param ContainerInterface $container
	 *
	 * @return ApplyController
	 */
    static public function factory(ContainerInterface $container)
    {
        $config            = $container->get('Config');
        $imageCacheManager = $container->get('Organizations\ImageFileCache\Manager');
        $validator         = $container->get('ValidatorManager');
        $repositories      = $container->get('repositories');
        $appEvents         = $container->get('Applications/Events');
        $viewHelper        = $container->get('ViewHelperManager');
        $uploadHandler     = $container->get(UploadHandler::class);

        return new self(
            $imageCacheManager,
            $validator,
            $repositories,
            $appEvents,
            $viewHelper,
            $uploadHandler,
            $config
        );
    }

	public function attachDefaultListeners()
	{
		parent::attachDefaultListeners();
		$events = $this->getEventManager();
		$events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
		return $this;
	}

    public function preDispatch(MvcEvent $e)
    {
        /* @var $application \Applications\Entity\Application */
        if ($this->params()->fromQuery('do')) {
            $e->getRouteMatch()->setParam('action', 'do');
            return;
        }

        /* @var $request    Request */
        /* @var $repository \Applications\Repository\Application */
        /* @var $container  Apply */
        $request      = $this->getRequest();
        $services     = $e->getApplication()->getServiceManager();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $container    = $services->get('forms')->get('Applications/Apply');

        if ($request->isPost()) {
            $appId = $this->params()->fromPost('applicationId');
            if (!$appId) {
                throw new RuntimeException('Missing application id.');
            }
            $routeMatch = $e->getRouteMatch();

            if ('recruiter-preview' == $appId) {
                $routeMatch->setParam('action', 'process-preview');
                return;
            }

            $application = $repository->find($appId);
            if (!$application) {
                throw new RuntimeException('Invalid application id.');
            }

            $action     = 'process';

            $routeMatch->setParam('action', $action);
        } else {
            $user  = $this->auth()->getUser();
            $appId = $this->params('applyId');
            if (!$appId) {
                throw new RuntimeException('Missing apply id');
            }

            /* @var Job $jobRepo */
            $jobRepo = $repositories->get('Jobs/Job');
            $job = $jobRepo->findOneByApplyId($appId);

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
                    /* @var $form UserInfo */
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
        $this->formContainer     = $container;
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
        /* @var Apply $form */
        $form        = $this->formContainer;
        $application = $form->getEntity(); /* @var \Applications\Entity\Application $application */

        $form->setParam('applicationId', $application->getId());

        $organizationImageCache = $this->imageCacheManager;

        $model = new ViewModel(
            [
            'form' => $form,
            'isApplicationValid' => $this->checkApplication($application),
            'application' => $application,
            'organizationImageCache' =>  $organizationImageCache
            ]
        );
        $model->setTemplate('applications/apply/index');
        return $model;
    }

    public function oneClickApplyAction()
    {
        /* @var \Applications\Entity\Application $application */
        $application = $this->formContainer->getEntity();
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

        $hybridAuth = $this->formContainer
            ->get('HybridAuthAdapter')
            ->getHybridAuth();
        /* @var $authProfile Hybrid_User_Profile */
        $authProfile = $hybridAuth->authenticate($network)
           ->getUserProfile();

        /* @var AbstractProfile $profile */
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
            $response = (new Client($authProfile->photoURL, ['sslverifypeer' => false]))->send();
            $file = new GridFSFile();
            $file->setBytes($response->getBody());

            $image = new Attachment();
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
    	$params = $this->params();
        $formName  = $params->fromQuery('form');
        $form      = $this->formContainer->getForm($formName);
        $postData  = $form->getOption('use_post_array') ? $params->fromPost() : array();
        $uploadHandler = $this->uploadHandler;

	    //@TODO: [ZF3] option use_files_array is false by default
        //$filesData = $form->getOption('use_files_array') ? $params->fromFiles() : array();

        $form->setData(array_merge($postData,$_FILES));
        if('contact.image' === $formName){
            $application = $uploadHandler->handleImageUpload($postData['applicationId'], $_FILES['image']);
            $form->getParent()->setEntity($application->getContact());
            $content = $this->viewHelper->get('form')->__invoke($form);
            return new JsonModel([
                'valid' => true,
                'content' => $content,
                'isApplicationValid' => $this->checkApplication($application)
            ]);
        }elseif('attachments' === $formName){
            $attachment = $uploadHandler->handleAttachmentUpload($postData['applicationId'], $_FILES['attachments']);
            $basepath = $this->viewHelper->get('basepath');
            $content = $basepath($attachment->getUri());
            $application = $uploadHandler->findApplication($postData['applicationId']);
            return new JsonModel([
                'valid' => true,
                'content' => $content,
                'isApplicationValid' => $this->checkApplication($application)
            ]);
        } else{
            if (!$form->isValid()) {
                return new JsonModel(
                    array(
                        'valid' => false,
                        'errors' => $form->getMessages(),
                    )
                );
            }
            $application = $this->formContainer->getEntity();
            $this->repositories->store($application);
        }

        if ($form instanceof SummaryForm) {
            $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
            $viewHelper = 'summaryForm';
        } else {
            $viewHelper = 'form';
        }
        $content = $this->viewHelper->get($viewHelper)->__invoke($form);

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
        $config       = $this->config;
        $repositories = $this->repositories;
        $repository   = $repositories->get('Applications/Application');
        $organizationImageCache = $this->imageCacheManager;
        /* @var Application $application*/
        $application  = $repository->findDraft(
            $this->auth()->getUser(),
            $this->params('applyId')
        );

        if (!$application) {
            throw new Exception('No application draft found.');
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
                'organizationImageCache' =>  $organizationImageCache,
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

        $repositories->store($application);

        $events   = $this->appEvents;
        $events->trigger(ApplicationEvent::EVENT_APPLICATION_POST_CREATE, $this, [ 'application' => $application ]);

        $model = new ViewModel(
            array(
            'success' => true,
            'application' => $application,
            'organizationImageCache' =>  $organizationImageCache,
            )
        );
        $model->setTemplate('applications/apply/index');

        return $model;
    }

    protected function checkApplication($application)
    {
        return $this->validator->get('Applications/Application')
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

        /** @var $settings Settings */
        $settings = ($user = $job->getUser()) ? $user->getSettings('Applications') : null;
        $formSettings = $settings ? $settings->getApplyFormSettings() : null;

        if ($formSettings && $formSettings->isActive()) {
            $container->disableElements($formSettings->getDisableElements());
            return;
        }

        $config = $this->config;
        $config = isset($config['form_elements_config']['Applications/Apply']['disable_elements'])
                ? $config['form_elements_config']['Applications/Apply']['disable_elements']
                : null;
        if ($config) {
            $container->disableElements($config);
        }
    }
}
