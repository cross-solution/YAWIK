<?php
/**
 * YAWIK
 * Core Module Bootstrap
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
use Core\Repository\DoctrineMongoODM\PersistenceListener;
use Core\Listener\NotificationAjaxHandler;
use Core\Listener\Events\NotificationEvent;
use Doctrine\ODM\MongoDB\Types\Type as DoctrineType;

/**
 * Bootstrap class of the Core module
 *
 */
class Module implements ConsoleBannerProviderInterface
{
    
    public function getConsoleBanner(Console $console)
    {
        
        $version = `git describe`;
        $name = 'YAWIK ' . trim($version);
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
        if (!DoctrineType::hasType('tz_date')) {
            DoctrineType::addType(
                'tz_date',
                '\Core\Repository\DoctrineMongoODM\Types\TimezoneAwareDate'
            );
        }
        
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator'); // initialise translator!
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        $eventManager        = $e->getApplication()->getEventManager();
        $sharedManager       = $eventManager->getSharedManager();
        
 #       $LogListener = new LogListener();
 #       $LogListener->attach($eventManager);
        
        if (!\Zend\Console\Console::isConsole()) {
            $redirectCallback = function () use ($e) {
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

        $notificationListener = $sm->get('Core/Listener/Notification');
        $notificationListener->attachShared($sharedManager);
        $notificationAjaxHandler = new NotificationAjaxHandler();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($notificationAjaxHandler, 'injectView'), -20);
        $notificationListener->attach(NotificationEvent::EVENT_NOTIFICATION_HTML, array($notificationAjaxHandler, 'render'), -20);
        
        $persistenceListener = new PersistenceListener();
        $persistenceListener->attach($eventManager);
        
        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            function ($event) {
                $application = $event->getApplication();
                if ($application::ERROR_EXCEPTION == $event->getError()) {
                    $ex = $event->getParam('exception');
                    if (404 == $ex->getCode()) {
                        $event->setError($application::ERROR_CONTROLLER_NOT_FOUND);
                    }
                }
            
            },
            500
        );
        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH,
            function ($event) use ($eventManager) {
                $eventManager->trigger('postDispatch', $event);
            },
            -150
        );
        
    }

    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        return $config;
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
                    'CoreTest' => __DIR__ . '/test/' . 'CoreTest'
                ),
            ),
        );
    }
}
