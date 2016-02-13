<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Controller;

use Auth\Controller\RegisterController;
use Auth\Form;
use Auth\Service;
use Auth\Options\ModuleOptions;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterControllerFactory implements FactoryInterface
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
         * @var $options  ModuleOptions
         */
        $formElementManager = $serviceLocator->get('formElementManager');
        $form = $formElementManager->get('Auth\Form\Register');

        $service = $serviceLocator->get('Auth\Service\Register');
        $logger = $serviceLocator->get('Core/Log');
        $options = $serviceLocator->get('Auth/Options');

        return new RegisterController($form, $service, $logger, $options);
    }
}
