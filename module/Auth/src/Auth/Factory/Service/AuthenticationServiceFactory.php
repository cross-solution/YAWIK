<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Auth\AuthenticationService;

/**
 * HybridAuth authentication adapter factory
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create an AuthenticationService service
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return AuthenticationService
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get('repositories')->get('Auth/User');
        $auth       = new AuthenticationService($repository);
        return $auth;
    }

    /**
     * Creates an instance of \Auth\Adapter\HybridAuth
     *
     * - injects the \HybridAuth
     * - injects the UserMapper fetched from the service manager.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Adapter\HybridAuth
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AuthenticationService::class);
    }
}
