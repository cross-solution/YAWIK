<?php

namespace Acl\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        $acl = new Acl();
        return $acl;
    }
}