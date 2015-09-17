<?php

namespace Acl\Factory\Service;

use Acl\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Permissions\Acl\Acl;

//use Acl\Adapter\Acl;

/**
 * authentication adapter factory
 */
class AclFactory implements FactoryInterface
{

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
        $assertions  = $serviceLocator->get('Acl\AssertionManager');
        $configArray = $serviceLocator->get('Config');
        
        if (!isset($configArray['acl'])) {
            throw new \OutOfRangeException('Missing index "acl" in config.');
        }
        
        $config = new Config($configArray['acl'], $assertions);
        $acl = $config->configureAcl(new Acl());
        
        return $acl;
    }
}
