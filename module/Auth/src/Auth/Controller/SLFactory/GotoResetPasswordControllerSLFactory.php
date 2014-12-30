<?php

namespace Auth\Controller\SLFactory;

use Auth\Controller\GotoResetPasswordController;
use Auth\Service;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GotoResetPasswordControllerSLFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GotoResetPasswordController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $service Service\GotoResetPassword
         * @var $logger  LoggerInterface
         */
        $service = $serviceLocator->get('Auth\Service\GotoResetPassword');
        $logger = $serviceLocator->get('Log/Core/Cam');

        return new GotoResetPasswordController($service, $logger);
    }
}