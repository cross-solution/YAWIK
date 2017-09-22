<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Auth\Factory\Controller;

use Auth\Form\UserStatusFieldset;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\LoggerInterface;
use Auth\Controller\IndexController;
use Auth\Form\Register;

class IndexControllerFactory implements FactoryInterface
{


    /**
     * Create an IndexController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return IndexController
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth = $container->get('AuthenticationService');
        $formElementManager = $container->get('FormElementManager');
        $loginForm = $formElementManager->get('Auth\Form\Login');
        $userLoginAdapter = $container->get('Auth/Adapter/UserLogin');
        $locale = $container->get('Core/Locale');
        $viewHelperManager = $container->get('ViewHelperManager');

        /* @var $logger LoggerInterface*/
        $logger = $container->get('Core/Log');

        /* @var $options \Auth\Options\ModuleOptions */
        $options = $container->get('Auth/Options');

        $forms[IndexController::LOGIN] = $loginForm;

        if ($options->getEnableRegistration()) {
            /* @var $registerForm Register */
            $registerForm = $formElementManager->get('Auth\Form\Register');
            $forms[IndexController::REGISTER] = $registerForm;
        }
        
        $hybridAuthAdapter = $container->get('HybridAuthAdapter');
		$externalAdapter = $container->get('ExternalApplicationAdapter');
		$repositories = $container->get('repositories');
        $controller = new IndexController($auth, $logger, $userLoginAdapter,$locale,$viewHelperManager,$forms, $options,$hybridAuthAdapter,$externalAdapter,$repositories);
        return $controller;
    }
    /**
     * Create controller
     *
     * @param ServiceLocatorInterface $controllerManager
     *
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        return $this($controllerManager, IndexController::class);
    }
}
