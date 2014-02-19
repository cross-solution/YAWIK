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
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Core\Listener\ErrorLoggerListener;
use Core\Listener\ErrorHandlerListener;
use Zend\Log\Formatter\ErrorHandler;

/**
 * Bootstrap class of the Core module
 * 
 */
class Module implements ConsoleBannerProviderInterface
{
    
    public function getConsoleBanner(Console $console) {
        
        $version = `git describe`;
        $name = 'Cross Applicant Management ' . trim($version);
        $width = $console->getWidth();
        return sprintf(
            "==%1\$s==\n%2\$s%3\$s\n**%1\$s**\n",
            str_repeat('-', $width - 4),
            str_repeat(' ', floor(($width - strlen($name)) / 2)),
            $name
       ); 
             
        
    }
    
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
        // Register the TimezoneAwareDate type with DoctrineMongoODM
        // Use it in Annotions ( @Field(type="tz_date") )
        \Doctrine\ODM\MongoDB\Types\Type::addType(
            'tz_date', 
            '\Core\Repository\DoctrineMongoODM\Types\TimezoneAwareDate'
        );
        
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator'); // initialise translator!
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        $eventManager        = $e->getApplication()->getEventManager();
        
 #       $LogListener = new LogListener();
 #       $LogListener->attach($eventManager);
        
        if (!\Zend\Console\Console::isConsole()) {
            
            $redirectCallback = function() use ($e) {
                $routeMatch = $e->getRouteMatch();
                $lang = $routeMatch ? $routeMatch->getParam('lang', 'en') : 'en';
                $uri    = $e->getRouter()->getBaseUrl() . '/' . $lang . '/error';
                
                header('Location: ' . $uri);
            };
            
            $errorHandlerListener = new ErrorHandlerListener($sm->get('ErrorLogger'), $redirectCallback);
            $errorHandlerListener->attach($eventManager);
            
            $languageRouteListener = new LanguageRouteListener();
            $languageRouteListener->attach($eventManager);
        
        
            $ajaxRenderListener = new AjaxRenderListener();
            $ajaxRenderListener->attach($eventManager);
        
            $enforceJsonResponseListener = new EnforceJsonResponseListener();
            $enforceJsonResponseListener->attach($eventManager);
        
            $stringListener = new StringListener();
            $stringListener->attach($eventManager);
        }
        
        $eventManager->attach('postDispatch', function ($event) use ($sm) {
            $sm->get('doctrine.documentmanager.odm_default')->flush();
        }, -150);
        
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
        $config = include __DIR__ . '/config/module.config.php';
        if (\Zend\Console\Console::isConsole()) {
            $config['doctrine']['configuration']['odm_default']['generate_proxies'] = false;
            $config['doctrine']['configuration']['odm_default']['generate_hydrators'] = false;
            
        }
        return $config;
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
