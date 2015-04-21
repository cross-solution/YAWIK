<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Auth\Factory\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\LoggerInterface;
use Auth\Controller\IndexController;

class IndexControllerFactory implements FactoryInterface {
    /**
     * Create controller
     *
     * @param ControllerManager $controllerManager
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator = $controllerManager->getServiceLocator();
        $auth = $serviceLocator->get('AuthenticationService');
        $loginForm = $serviceLocator->get('Auth\Form\Login');
        /* @var $logger LoggerInterface*/
        $logger = $serviceLocator->get('Core/Log');
        $controller = new IndexController($auth,$logger,$loginForm);
        return $controller;
    }

}