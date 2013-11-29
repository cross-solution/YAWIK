<?php

namespace Core\src\Core\Service;

use Zend\Mvc\Service\ModuleManagerFactory as ZendModuleManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\EventManager\Event;

class ModuleManagerFactory extends ZendModuleManagerFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleManager = parent::createService($serviceLocator);
        
        $events = $moduleManager->getEventManager();
        $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($this, 'onLoadModules'), 1000);
        
        return $moduleManager;
    }
    
    public function onLoadModules(Event $e)
    {
        $moduleManager = $e->getTarget();
        $modules = $moduleManager->getModules();
        // Hier kommt die Superglobal $_ENV rein
        
        $moduleManager->setModules($modules);
    }
}