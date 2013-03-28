<?php
/**
 * Cross Applicant Management
 * Core Module Bootstrap
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core */
namespace Core;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Core\Service\LanguageRouteListener;
use Core\Listener\AttachJsonStrategyListener;

/**
 * Bootstrap class of the Core module
 * 
 */
class Module
{
    /**
     * Sets up services on the bootstrap event.
     * 
     * @internal
     *     Creates the translation service and a ModuleRouteListener
     *      
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $sm->get('translator'); // initialise translator!
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $languageRouteListener = new LanguageRouteListener();
        $languageRouteListener->attach($eventManager);
        
        $attachJsonStrategyListener = new AttachJsonStrategyListener();
        $attachJsonStrategyListener->attach($eventManager);
    }

    /**
     * Loads module specific configuration.
     * 
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Loads module specific autoloader configuration.
     * 
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
}
