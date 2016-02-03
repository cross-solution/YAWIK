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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\LoggerInterface;
use Auth\Controller\IndexController;
use Auth\Form\Register;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * Create controller
     *
     * @param ServiceLocatorInterface $controllerManager
     *
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $controllerManager->getServiceLocator();
        $auth = $serviceLocator->get('AuthenticationService');
        $formElementManager = $serviceLocator->get('formElementManager');
        $loginForm = $formElementManager->get('Auth\Form\Login');

        /* @var $logger LoggerInterface*/
        $logger = $serviceLocator->get('Core/Log');

        /* @var $options \Auth\Options\ModuleOptions */
        $options = $serviceLocator->get('Auth/Options');

        $forms[IndexController::LOGIN] = $loginForm;

        if ($options->getEnableRegistration()) {
            /* @var $registerForm Register */
            $registerForm = $formElementManager->get('Auth\Form\Register');
            $forms[IndexController::REGISTER] = $registerForm;
        }

        $controller = new IndexController($auth, $logger, $forms, $options);
        return $controller;
    }
}
