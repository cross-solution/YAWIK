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
use Auth\Form\SocialProfiles;
use Auth\Form\UserProfileContainer;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\HelperPluginManager;
use Zend\View\Model\JsonModel;
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
		$controller = new ManageController(
			$userProfileContainer,
			$authService,
			$repositories,
			$socialProfileForm,
			$translator,
			$viewHelper,
			$hybridAuthAdapter
		);
		return $controller;
	}
	
	public function __construct(
		UserProfileContainer $userProfileContainer,
		AuthenticationService $authService,
		RepositoryService $repositories,
		SocialProfiles $socialProfileForm,
		Translator $translator,
		HelperPluginManager $viewHelper,
		HybridAuth $hybridAuthAdapter
	)
	{
		$this->userProfileContainer = $userProfileContainer;
		$this->authService = $authService;
		$this->socialProfileForm = $socialProfileForm;
		$this->repositories = $repositories;
		$this->translator = $translator;
		$this->viewHelper = $viewHelper;
		$this->hybridAuthAdapter = $hybridAuthAdapter;
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
            
            if ($form) {
                $postData  = $form->getOption('use_post_array') ? $_POST : array();
                //@TODO: [ZF3] option use_files_array is false by default
                //$filesData = $form->getOption('use_files_array') ? $_FILES : array();
	            $filesData = $_FILES;
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
                                    'data' => \Zend\Json\Json::decode($dataProfiles[$network])
                                ];
                                $user->addProfile($network, $profile);
                            }
                        }
                    }
                }
                
                // keep data in sync & properly decoded
                $formSocialProfiles->setData(['social_profiles' => array_map(function ($array)
                {
                    return \Zend\Json\Json::decode($array) ?: '';
                }, $dataProfiles)]);
            }
        }
        
        return array(
            'form' => $userProfileContainer,
            'socialProfilesForm' => $formSocialProfiles
        );
    }
}
