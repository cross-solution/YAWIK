<?php

/**
 * 
 */

namespace Settings\Controller\Plugins;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
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
        $settings = $serviceLocator->getServiceLocator()->get('Settings');
        return $settings;
    }
}