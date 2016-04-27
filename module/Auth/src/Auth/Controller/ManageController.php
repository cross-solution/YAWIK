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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Core\Form\SummaryFormInterface;

/**
 * Main Action Controller for Authentication module.
 *
 */
class ManageController extends AbstractActionController
{
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * @return array|JsonModel
     */
    public function profileAction()
    {
        $serviceLocator = $this->getServiceLocator();
        $forms = $serviceLocator->get('forms');
        $container = $forms->get('Auth/userprofilecontainer');
        $user = $serviceLocator->get('AuthenticationService')->getUser(); /* @var $user \Auth\Entity\User */
        $postProfiles = (array)$this->params()->fromPost('social_profiles');
        $userProfiles = $user->getProfile();
        $formSocialProfiles = $forms->get('Auth/SocialProfiles')
            ->setUseDefaultValidation(true)
            ->setData(['social_profiles' => array_map(function ($array)
            {
                return $array['data'];
            }, $userProfiles)]);
        
        $container->setEntity($user);

        if ($this->request->isPost()) {
            $formName  = $this->params()->fromQuery('form');
            $form      = $container->getForm($formName);
            
            if ($form) {
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
                
                $serviceLocator->get('repositories')->store($user);
                
                if ('file-uri' === $this->params()->fromPost('return')) {
                    $content = $form->getHydrator()->getLastUploadedFile()->getUri();
                } else {
                    if ($form instanceof SummaryFormInterface) {
                        $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                        $viewHelper = 'summaryform';
                    } else {
                        $viewHelper = 'form';
                    }
                    $content = $serviceLocator->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
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
                    $userRepository = $serviceLocator->get('repositories')->get('Auth/User'); /* @var $userRepository \Auth\Repository\User */
                    $hybridAuth = $serviceLocator->get('HybridAuthAdapter')
                        ->getHybridAuth();
                    
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
                                        $network => [sprintf(/*@translate*/ 'Could not connect your %s profile with your user account. The profile is already connected to another user account.', $authProfile->displayName)]
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
            'form' => $container,
            'socialProfilesForm' => $formSocialProfiles
        );
    }
}
