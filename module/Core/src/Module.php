<?php
/**
 * YAWIK
 * Core Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

/** Core */
namespace Core;

use Core\Console\Application;
use Core\Console\ConsoleCommandProviderInterface;
use Core\Console\InstallAssetsCommand;
use Core\Listener\AjaxRouteListener;
use Zend\EventManager\Event;
use Zend\Mvc\MvcEvent;
use Core\Listener\LanguageRouteListener;
use Core\Listener\AjaxRenderListener;
use Core\Listener\XmlRenderListener;
use Core\Listener\EnforceJsonResponseListener;
use Core\Listener\StringListener;
use Core\Listener\TracyListener;
use Core\Service\Tracy as TracyService;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Core\Listener\ErrorHandlerListener;
use Core\Repository\DoctrineMongoODM\PersistenceListener;
use Core\Listener\NotificationAjaxHandler;
use Core\Listener\Events\NotificationEvent;
use Doctrine\ODM\MongoDB\Types\Type as DoctrineType;

/**
 * Bootstrap class of the Core module
 *
 */
class Module implements ConsoleBannerProviderInterface, ConsoleCommandProviderInterface
{
    public function getConsoleBanner(Console $console)
    {
        $version = `git describe 2>/dev/null`;
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
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Register the TimezoneAwareDate type with DoctrineMongoODM
        // Use it in Annotations ( @Field(type="tz_date") )
        if (!DoctrineType::hasType('tz_date')) {
            DoctrineType::addType(
                'tz_date',
                '\Core\Repository\DoctrineMongoODM\Types\TimezoneAwareDate'
            );
        }
        
        $sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator'); // initialize translator!
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        $eventManager        = $e->getApplication()->getEventManager();
        $sharedManager       = $eventManager->getSharedManager();

        $coreOptions = $sm->get('Core/Options');
        $tracyConfig = $sm->get('Config')['tracy'];
        $logDir = $coreOptions->getLogDir().'/tracy';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $tracyConfig['log'] = $logDir;

        (new TracyService())->register($tracyConfig);
        (new TracyListener())->attach($eventManager);
        
        if (!\Zend\Console\Console::isConsole()) {
            (new ErrorHandlerListener())->attach($eventManager);

            /* @var \Core\Options\ModuleOptions $options */
            $languageRouteListener = new LanguageRouteListener(
                $sm->get('Core/Locale'),
                $sm->get('Core/Options')
            );
            $languageRouteListener->attach($eventManager);
        
            $ajaxRenderListener = new AjaxRenderListener();
            $ajaxRenderListener->attach($eventManager);

            $ajaxRouteListener = $sm->get(AjaxRouteListener::class);
            $ajaxRouteListener->attach($eventManager);

            $xmlRenderListener = new XmlRenderListener();
            $xmlRenderListener->attach($eventManager);
        
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
        

        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            function ($event) {
                if ($event instanceof MvcEvent) {
                    $application = $event->getApplication();
                    
                    if ($application::ERROR_EXCEPTION == $event->getError()) {
                        $ex = $event->getParam('exception');
                        if (404 == $ex->getCode()) {
                            $event->setError($application::ERROR_CONTROLLER_NOT_FOUND);
                        }
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
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    /**
     * @inheritdoc
     */
    public function registerCommands(Application $application)
    {
        $application->add(new InstallAssetsCommand());
    }
}
