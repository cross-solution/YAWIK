<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Controller;

use Auth\Controller\GotoResetPasswordController;
use Auth\Service;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GotoResetPasswordControllerFactory implements FactoryInterface
{
    /**
     * Create a GotoResetPasswordController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return GotoResetPasswordController
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var Service\GotoResetPassword $service
         * @var LoggerInterface $logger
         */
        $service = $container->get('Auth\Service\GotoResetPassword');
        $logger = $container->get('Core/Log');

        return new GotoResetPasswordController($service, $logger);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GotoResetPasswordController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, GotoResetPasswordController::class);
    }
}
