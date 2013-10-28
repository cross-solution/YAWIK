<?php

/**
 * 
 */
namespace Settings\Settings;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
//use Settings\Repository\Settings;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;

class SettingsFactory implements FactoryInterface
{
    
    /**
     * Create the settings service
     * 
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $settings = new Settings;
        $settings->setRepository($serviceLocator->get('RepositoryManager')->get('SettingsRepository'));
        $settings->setAuth($serviceLocator->get('AuthenticationService'));
        //$plugin->setLocator($serviceLocator);
        return $settings;
    }
}