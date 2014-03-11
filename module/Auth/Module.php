<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth;

use Zend\Mvc\MvcEvent;
use Auth\View\InjectLoginInfoListener;
use Auth\Listener\TokenListener;
use Auth\Listener\UnauthorizedAccessListener;
/**
 * Bootstrap class of the Core module
 * 
 */
class Module
{

    public function init(\Zend\ModuleManager\ModuleManagerInterface $moduleManager)
    {
        if (\Zend\Console\Console::isConsole()) {
            return;
        }
        
        $eventManager  = $moduleManager->getEventManager()->getSharedManager();
        $tokenListener = new TokenListener();
        $tokenListener->attachShared($eventManager);
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
            'Zend\Loader\ClassMapAutoloader' => array(
                // This is an hack due to bad design of Hybridauth
                // This ensures the class from "addtional-providers" is loaded.
                array(
                    'Hybrid_Providers_XING'
                    => __DIR__ . '/../../vendor/hybridauth/hybridauth/additional-providers/hybridauth-xing/Providers/XING.php',
                ),
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Acl' => __DIR__ . '/src/Acl'
                ),
            ),
        );
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        if (\Zend\Console\Console::isConsole()) {
            return;
        }
        $eventManager = $e->getApplication()->getEventManager();
        $services     = $e->getApplication()->getServiceManager();
        
        // TODO: Löschen sobald die Lösung mit der LoginBox klappt
        //$eventManager->attach(
        //    array(MvcEvent::EVENT_RENDER, MvcEvent::EVENT_RENDER_ERROR),
        //    array(new InjectLoginInfoListener(), 'injectLoginInfo'), -1000
        //);
        
        $checkPermissionsListener = $services->get('Auth/CheckPermissionsListener');
        $checkPermissionsListener->attach($eventManager);
        
        $unauthorizedAccessListener = $services->get('UnauthorizedAccessListener');
        $unauthorizedAccessListener->attach($eventManager);
        
    }
    
}
