<?php

namespace Core\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Settings\Repository\Settings;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;

class ConfigFactory implements FactoryInterface
{
    /**
     * Create the settings service
     * 
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $plugin = new Config($config);

        return $plugin;
    }
}