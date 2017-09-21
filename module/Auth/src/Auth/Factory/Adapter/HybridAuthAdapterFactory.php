<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Adapter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Adapter\HybridAuth as HybridAuthAdapter;

/**
 * HybridAuth authentication adapter factory
 */
class HybridAuthAdapterFactory implements FactoryInterface
{
    /**
     * Create a HybridAuthAdapter adapter
     *
     * authentication with HybridAuth
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return HybridAuthAdapter
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = new HybridAuthAdapter();
        $adapter->setHybridAuth($container->get('HybridAuth'));
        $adapter->setRepository($container->get('repositories')->get('Auth/User'));
        $adapter->setSocialProfilePlugin($container->get('ControllerPluginManager')->get('Auth/SocialProfiles'));
        return $adapter;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Adapter\HybridAuth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, HybridAuthAdapter::class);
    }
}
