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
use Interop\Container\ContainerInterface;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordControllerFactory implements FactoryInterface
{
    /**
     * Create a ForgotPasswordController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ForgotPasswordController
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var $form    Form\ForgotPassword
         * @var $service Service\ForgotPassword
         * @var $logger  LoggerInterface
         */
        $form = $container->get('Auth\Form\ForgotPassword');
        $service = $container->get('Auth\Service\ForgotPassword');
        $logger = $container->get('Core/Log');

        return new ForgotPasswordController($form, $service, $logger);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForgotPasswordController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ForgotPasswordController::class);
    }
}
