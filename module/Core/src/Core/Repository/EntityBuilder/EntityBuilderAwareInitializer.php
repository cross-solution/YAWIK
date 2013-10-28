<?php

namespace Core\Repository\EntityBuilder;


use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityBuilderAwareInitializer implements InitializerInterface
{
    
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceOf EntityBuilderAwareInterface) {
            $entityBuilderManager = $serviceLocator instanceOf EntityBuilderManager
                                  ? $serviceLocator
                                  : $serviceLocator->getServiceLocator()->get('builders');
            $instance->setEntityBuilderManager($entityBuilderManager);
        }
    }
    
}