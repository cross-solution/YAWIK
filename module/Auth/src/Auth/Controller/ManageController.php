<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Auth\Adapter\HybridAuth;
use Auth\AuthenticationService;
use Auth\Entity\UserImage;
use Auth\Entity\UserInterface;
use Auth\Form\SocialProfiles;
use Auth\Form\UserProfileContainer;
use Auth\Service\UploadHandler;
use Core\Entity\ImageMetadata;
use Core\Repository\RepositoryService;
use Core\Service\FileManager;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\HelperPluginManager;
use Laminas\View\Model\JsonModel;
use Core\Form\SummaryFormInterface;

/**
 * Main Action Controller for Authentication module.
 *
 */
class ManageController extends AbstractActionController
{
	private $userProfileContainer;
	
	private $authService;
	
	private $socialProfileForm;
	
	private $translator;
	
	private $repositories;
	
	private $viewHelper;
	
	private $hybridAuthAdapter;

    private UploadHandler $manageHandler;

    /**
	 * @param ContainerInterface $container
	 * @return ManageController
	 */
	static public function factory(ContainerInterface $container)
	{
		$forms = $container->get('forms');
		$userProfileContainer = $forms->get('Auth/UserProfileContainer');
		$socialProfileForm = $forms->get('Auth/SocialProfiles');
		$authService = $container->get('AuthenticationService');
		$translator = $container->get('translator');
		$repositories = $container->get('repositories');
		$viewHelper = $container->get('ViewHelperManager');
		$hybridAuthAdapter = $container->get('HybridAuthAdapter');
		$uploadHandler = $container->get(UploadHandler::class);

		return new ManageController(
			$userProfileContainer,
			$authService,
			$repositories,
			$socialProfileForm,
			$translator,
			$viewHelper,
			$hybridAuthAdapter,
            $uploadHandler
		);
	}
	
	public function __construct(
        UserProfileContainer $userProfileContainer,
        AuthenticationService $authService,
        RepositoryService $repositories,
        SocialProfiles $socialProfileForm,
        Translator $translator,
        HelperPluginManager $viewHelper,
        HybridAuth $hybridAuthAdapter,
        UploadHandler $manageHandler
	)
	{
		$this->userProfileContainer = $userProfileContainer;
		$this->authService = $authService;
		$this->socialProfileForm = $socialProfileForm;
		$this->repositories = $repositories;
		$this->translator = $translator;
		$this->viewHelper = $viewHelper;
		$this->hybridAuthAdapter = $hybridAuthAdapter;
        $this->manageHandler = $manageHandler;
    }
	
	/**
     * @return array|JsonModel
     */
    public function profileAction()
    {
        /* @var \Auth\Form\UserProfileContainer $userProfileContainer */
        $userProfileContainer = $this->userProfileContainer;
        $user = $this->authService->getUser(); /* @var $user \Auth\Entity\User */
        $postProfiles = (array)$this->params()->fromPost('social_profiles');
        $userProfiles = $user->getProfile();
        $formSocialProfiles = $this->socialProfileForm
            ->setUseDefaultValidation(true)
            ->setData(['social_profiles' => array_map(function ($array)
            {
                return $array['data'];
            }, $userProfiles)]);
        
        $translator = $this->translator;
        /* @var \Auth\Form\SocialProfiles $formSocialProfiles */
        $formSocialProfiles->getBaseFieldset()
            ->setOption(
                'description',
                $translator->translate('You can connect your user profile with social networks. This allows you to log in via these networks.')
            );
        $userProfileContainer->setEntity($user);

        if ($this->request->isPost()) {
            $formName  = $this->params()->fromQuery('form');
            $form      = $userProfileContainer->getForm($formName);

            if(!is_null($form) && 'info.image' === $formName) {
                $user = $this->manageHandler->handleUpload($user, $_FILES['image']);
                $form->getParent()->setEntity($user->getInfo());
                $content = $this->viewHelper->get('form')->__invoke($form);
                return new JsonModel(
                    array(
                        'valid' => true,
                        'content' => $content,
                    )
                );
            }elseif($form) {
                $postData  = $form->getOption('use_post_array') ? $_POST : array();
                //@TODO: [ZF3] option use_files_array is false by default
                //$filesData = $form->getOption('use_files_array') ? $_FILES : array();
	            $filesData = $_FILES;
                $data      = array_merge($postData, $filesData);
                $form->setData($data);

                if (!$form->isValid()) {
                    return new JsonModel(array(
                        'valid' => false,
                        'errors' => $form->getMessages(),
                    ));
                }

                $this->repositories->store($user);
                
                if ('file-uri' === $this->params()->fromPost('return')) {
                    $content = $form->getHydrator()->getLastUploadedFile()->getUri();
                } else {
                    if ($form instanceof SummaryFormInterface) {
                        $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                        $viewHelper = 'summaryForm';
                    } else {
                        $viewHelper = 'form';
                    }
                    $content = $this->viewHelper->get($viewHelper)->__invoke($form);
                }
                
                return new JsonModel(
                    array(
                    'valid' => $form->isValid(),
                    'content' => $content,
                    )
                );
            }
            elseif ($postProfiles) {
                $formSocialProfiles->setData($this->params()->fromPost());
                
                if ($formSocialProfiles->isValid()) {
                    $dataProfiles = $formSocialProfiles->getData()['social_profiles'];
                    $userRepository = $this->repositories->get('Auth/User'); /* @var $userRepository \Auth\Repository\User */
                    $hybridAuth = $this->hybridAuthAdapter->getHybridAuth();
                    
                    foreach ($dataProfiles as $network => $postProfile) {
                        // remove
                        if (isset($userProfiles[$network]) && !$dataProfiles[$network]) {
                            $user->removeProfile($network);
                        }
                        
                        // add
                        if (!isset($userProfiles[$network]) && $dataProfiles[$network]) {
                            $authProfile = $hybridAuth->authenticate($network)
                                ->getUserProfile();
                            // check for existing profiles
                            if ($userRepository->isProfileAssignedToAnotherUser($user->getId(), $authProfile->identifier, $network)) {
                                $dataProfiles[$network] = null;
                                $formSocialProfiles->setMessages(array(
                                    'social_profiles' => [
                                        $network => [sprintf($translator->translate('Could not connect your %s profile with your user account. The profile is already connected to another user account.'), $authProfile->displayName)]
                                    ]
                                ));
                            } else {
                                $profile = [
                                    'auth' => (array)$authProfile,
                                    'data' => \Laminas\Json\Json::decode($dataProfiles[$network])
                                ];
                                $user->addProfile($network, $profile);
                            }
                        }
                    }
                }
                
                // keep data in sync & properly decoded
                $formSocialProfiles->setData(['social_profiles' => array_map(function ($array)
                {
                    return \Laminas\Json\Json::decode($array) ?: '';
                }, $dataProfiles)]);
            }
        }
        
        return array(
            'form' => $userProfileContainer,
            'socialProfilesForm' => $formSocialProfiles
        );
    }
}
