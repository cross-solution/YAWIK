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

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
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
        $formElementManager = $container->get('formElementManager');
        $loginForm = $formElementManager->get('Auth\Form\Login');

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

        $controller = new IndexController($auth, $logger, $forms, $options);
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
        return $this($controllerManager->getServiceLocator(), UserStatusFieldset::class);
    }
}
