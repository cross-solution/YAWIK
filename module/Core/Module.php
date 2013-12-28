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

use Zend\Mvc\MvcEvent;
use Core\Listener\LanguageRouteListener;
use Core\Listener\AjaxRenderListener;
use Core\Listener\LogListener;
use Core\Listener\EnforceJsonResponseListener;
use Core\Listener\StringListener;

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
        $translator = $sm->get('translator'); // initialise translator!
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        $eventManager        = $e->getApplication()->getEventManager();
        
 #       $LogListener = new LogListener();
 #       $LogListener->attach($eventManager);
        
        $languageRouteListener = new LanguageRouteListener();
        $languageRouteListener->attach($eventManager);
        
        $ajaxRenderListener = new AjaxRenderListener();
        $ajaxRenderListener->attach($eventManager);
        
        $enforceJsonResponseListener = new EnforceJsonResponseListener();
        $enforceJsonResponseListener->attach($eventManager);
        
        $stringListener = new StringListener();
        $stringListener->attach($eventManager);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function ($event) use ($eventManager) {
            $eventManager->trigger('postDispatch', $event);
        }, -150);
        
        
        
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
