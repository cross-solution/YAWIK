<?php

namespace Core\src\Core\mvc\Service;

use Traversable;
use Zend\ModuleManager\Listener\ServiceListener as DefaultServiceListener;
use Zend\EventManager\EventManagerInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

class ServiceListener extends DefaultServiceListener 
{   
    public function attach(EventManagerInterface $events)
    {
        parent::attach($events);
        //$this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, array($this, 'onLoadModule'));
        //$this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($this, 'initializeModules'), -900);
        return $this;
    }
    
    public function onLoadModule(ModuleEvent $e)
    {
        $module = $e->getModule();

        if (!$module instanceof ConfigProviderInterface
            && !is_callable(array($module, 'getConfig'))
        ) {
            return $this;
        }

        $config = $module->getConfig();
        // TODO: hier wegspeichern der Configs lokal
        //$this->addConfig($e->getModuleName(), $config);

        return $this;
    }
    
    public function initializeModules(ModuleEvent $e)
    {
        // TODO: hier aus den lokalen Configs die entsprechenden Services generieren
        // meist Callbacks 
        //$this->defaultServiceManager->setService('moduleConfigs',array());
        
    }
}