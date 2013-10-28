<?php

namespace Core\Repository\Mapper;


use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MapperAwareInitializer implements InitializerInterface
{
    
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceOf MapperAwareInterface) {
            $mapperManager = $serviceLocator instanceOf MapperManager
                           ? $serviceLocator
                           : $serviceLocator->getServiceLocator()->get('mappers');
            $instance->setMapperManager($mapperManager);
        }
    }
    
}