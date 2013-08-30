<?php

/**
 * 
 */

namespace Settings\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Settings\Repository\Settings;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;

class SettingsPluginFactory implements FactoryInterface
{
    /**
     * Create the settings service
     * 
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugin = new SettingsPlugin;
        $plugin->setRepository($serviceLocator->getServiceLocator()->get('SettingsRepository'));
        $plugin->setAuth($serviceLocator->getServiceLocator()->get('AuthenticationService'));
        //$plugin->setLocator($serviceLocator);
        return $plugin;
    }
}