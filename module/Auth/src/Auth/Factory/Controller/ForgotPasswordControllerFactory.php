<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Controller;

use Auth\Controller\ForgotPasswordController;
use Auth\Form;
use Auth\Service;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForgotPasswordController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $form    Form\ForgotPassword
         * @var $service Service\ForgotPassword
         * @var $logger  LoggerInterface
         */
        $form = $serviceLocator->get('Auth\Form\ForgotPassword');
        $service = $serviceLocator->get('Auth\Service\ForgotPassword');
        $logger = $serviceLocator->get('Core/Log');

        return new ForgotPasswordController($form, $service, $logger);
    }
}
