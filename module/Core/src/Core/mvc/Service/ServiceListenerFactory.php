<?php

namespace Core\src\Core\mvc\Service;

use Zend\Mvc\Exception\RuntimeException;
use Zend\Mvc\Exception\InvalidArgumentException;
//use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Service\ServiceListenerFactory as DefaultServiceListenerFactory;


class ServiceListenerFactory extends DefaultServiceListenerFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration   = $serviceLocator->get('ApplicationConfig');
        
        // den eigenen ServiceListener einsetzen, ich habe bisher keine Möglichkeit gefunden
        // das über Invokables oder Factories zu machen
        $serviceListener = new ServiceListener($serviceLocator, $this->defaultServiceConfig);
        $serviceLocator-> setService('ServiceListenerInterface', $serviceListener);
        
        return parent::createService($serviceLocator);
    }
}
