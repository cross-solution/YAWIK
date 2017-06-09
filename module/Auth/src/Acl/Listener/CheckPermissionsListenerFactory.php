<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Acl\Listener;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Auth view helper.
 */
class CheckPermissionsListenerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $acl          = $container->get('Acl');
        $user         = $container->get('AuthenticationService')->getUser();
        $config       = $container->get('Config');
        $exceptionMap = isset($config['acl']['exceptions']) ? $config['acl']['exceptions'] : array();
        $listener = new CheckPermissionsListener($acl, $user, $exceptionMap);

        return $listener;
    }

    /**
     * Creates an instance of \Auth\View\Helper\Auth
     *
     * - Injects the AuthenticationService
     *
     * @param ServiceLocatorInterface $helpers
     * @return \Auth\View\Helper\Auth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, CheckPermissionsListener::class);
    }
}
