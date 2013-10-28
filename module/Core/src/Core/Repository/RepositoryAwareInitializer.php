<?php

namespace Core\Repository;


use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryAwareInitializer implements InitializerInterface
{
    
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceOf RepositoryAwareInterface) {
            $repositoryManager = $serviceLocator instanceOf RepositoryManager
                               ? $serviceLocator
                               : $serviceLocator->getServiceLocator()->get('repositories');
            $instance->setRepositoryManager($repositoryManager);
        }
    }
    
}