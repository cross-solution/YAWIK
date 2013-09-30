<?php
/**
 * 
 */

namespace Settings\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Settings\Repository\Settings;

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
        $settings->setServiceLocator($serviceLocator->getServiceLocator());
        $settings->setUserRepository($serviceLocator->getServiceLocator()->get('RepositoryManager')->get('User'));
        $config = $serviceLocator->getServiceLocator()->get('Config');
        
        // put on the Listener for saving the entity
        $application = $serviceLocator->getServiceLocator()->get('Application');
        $events = $application->getEventManager();
        $events->attach('postDispatch', array($settings, 'onPostDispatch'));
        return $settings;
    }
}