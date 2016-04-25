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
        $services = $this->getServiceLocator();
        $forms = $services->get('forms');
        $container = $forms->get('Auth/userprofilecontainer');
        $user = $services->get('AuthenticationService')->getUser(); /* @var $user \Auth\Entity\User */
        $postProfiles = (array)$this->params()->fromPost('social_profiles');
        $formSocialProfiles = $forms->get('Auth/SocialProfiles')
            ->setUseDefaultValidation(true)
            ->setData(['social_profiles' => array_map(function ($array)
            {
                return $array['data'];
            }, $user->getProfiles())]);
        
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
                
                $this->getServiceLocator()->get('repositories')->store($user);
                
                if ('file-uri' === $this->params()->fromPost('return')) {
                    $content = $form->getHydrator()->getLastUploadedFile()->getUri();
                } else {
                    if ($form instanceof SummaryFormInterface) {
                        $form->setRenderMode(SummaryFormInterface::RENDER_SUMMARY);
                        $viewHelper = 'summaryform';
                    } else {
                        $viewHelper = 'form';
                    }
                    $content = $this->getServiceLocator()->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
                }
                
                return new JsonModel(
                    array(
                    'valid' => $form->isValid(),
                    'content' => $content,
                    )
                );
            }
            elseif ($postProfiles) {
                $userProfiles = $user->getProfiles();
                $formSocialProfiles->setData($this->params()->fromPost());
                
                if ($formSocialProfiles->isValid()) {
                    $dataProfiles = $formSocialProfiles->getData()['social_profiles'];
                    $serviceLocator = $this->getServiceLocator();
                    $hybridAuth = $serviceLocator->get('HybridAuthAdapter')
                        ->getHybridAuth();
                    
                    foreach ($dataProfiles as $network => $postProfile) {
                        // remove
                        if (isset($userProfiles[$network]) && !$dataProfiles[$network]) {
                            $user->removeProfile($network);
                        }
                        
                        // add
                        if (!isset($userProfiles[$network]) && $dataProfiles[$network]) {
                            $adapter = $hybridAuth->authenticate($network);
                            $profile = [
                                'auth' => (array)$adapter->getUserProfile(),
                                'data' => \Zend\Json\Json::decode($dataProfiles[$network])
                            ];
                            $user->addProfile($network, $profile);
                        }
                    }
                }
            }
        }
        
        return array(
            'form' => $container,
            'socialProfilesForm' => $formSocialProfiles
        );
    }
}
