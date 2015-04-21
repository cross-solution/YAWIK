<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Controller\SLFactory;

use Auth\Controller\RegisterConfirmationController;
use Auth\Service;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterConfirmationControllerSLFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RegisterConfirmationController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $service Service\RegisterConfirmation
         * @var $logger  LoggerInterface
         */
        $service = $serviceLocator->get('Auth\Service\RegisterConfirmation');
        $logger = $serviceLocator->get('Core/Log');

        return new RegisterConfirmationController($service, $logger);
    }
}