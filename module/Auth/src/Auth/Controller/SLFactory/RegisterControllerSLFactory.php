<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Controller\SLFactory;

use Auth\Controller\RegisterController;
use Auth\Form;
use Auth\Service;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterControllerSLFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RegisterController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $form    Form\Register
         * @var $service Service\Register
         * @var $logger  LoggerInterface
         */
        $form = $serviceLocator->get('Auth\Form\Register');
        $service = $serviceLocator->get('Auth\Service\Register');
        $logger = $serviceLocator->get('Log/Core/Cam');

        return new RegisterController($form, $service, $logger);
    }
}