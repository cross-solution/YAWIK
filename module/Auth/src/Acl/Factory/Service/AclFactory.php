<?php

namespace Acl\Factory\Service;

use Acl\Config;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Permissions\Acl\Acl;
//use Acl\Service\Acl;

/**
 * authentication adapter factory
 */
class AclFactory implements FactoryInterface
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
        $assertions  = $container->get('Acl\AssertionManager');
        $configArray = $container->get('Config');

        if (!isset($configArray['acl'])) {
            throw new \OutOfRangeException('Missing index "acl" in config.');
        }

        $config = new Config($configArray['acl'], $assertions);
        $acl = $config->configureAcl(new Acl());

        return $acl;
    }


    /**
     * Creates an instance of \Auth\Adapter\ExternalApplication
     *
     * - injects the UserRepository fetched from the service manager.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Adapter\ExternalApplication
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Acl::class);
    }
}
