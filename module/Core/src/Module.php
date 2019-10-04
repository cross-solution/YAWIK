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

use Core\Listener\AjaxRouteListener;
use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use Core\Options\ModuleOptions;
use Yawik\Composer\RequireDirectoryPermissionInterface;
use Yawik\Composer\RequireFilePermissionInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Core\Listener\LanguageRouteListener;
use Core\Listener\AjaxRenderListener;
use Core\Listener\XmlRenderListener;
use Core\Listener\EnforceJsonResponseListener;
use Core\Listener\StringListener;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Core\Listener\ErrorHandlerListener;
use Core\Listener\NotificationAjaxHandler;
use Core\Listener\Events\NotificationEvent;
use Doctrine\ODM\MongoDB\Types\Type as DoctrineType;
use Zend\Stdlib\ArrayUtils;

/**
 * Bootstrap class of the Core module
 */
class Module implements
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface,
    RequireFilePermissionInterface,
    RequireDirectoryPermissionInterface,
    VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = '0.33.20';

    /**
     * @param ModuleOptions $options
     * @inheritdoc
     * @return array
     */
    public function getRequiredFileLists(ModuleOptions $options)
    {
        return [
            $options->getLogDir().'/yawik.log'
        ];
    }

    /**
     * @param ModuleOptions $options
     * @return array
     */
    public function getRequiredDirectoryLists(ModuleOptions $options)
    {
        return [
            $options->getConfigDir().'/autoload',
            $options->getCacheDir(),
            $options->getLogDir(),
            $options->getLogDir().'/tracy'
        ];
    }


    public function getConsoleBanner(Console $console)
    {
        $name = Application::getCompleteVersion();
        $width = $console->getWidth();
        return sprintf(
            "==%1\$s==\n%2\$s%3\$s\n**%1\$s**\n",
            str_repeat('-', $width - 4),
            str_repeat(' ', floor(($width - strlen($name)) / 2)),
            $name
        );
    }

    public function getConsoleUsage(Console $console)
    {
        return [
            'purge [--no-check] [--options=] <entity> [<id>]'  => 'Purge entities',
            'This command will load entities to be purged, checks the dependency of each and removes all entities completely from the',
            'database. However, called with no <entity> and options it will output a list of all available entity loaders and its options.',
            '',
            ['--no-check', 'Skip the dependency check and remove all entities and dependencies straight away.'],
            ['--options=STRING', 'JSON string represents options for the specific entity loader used.'],
            "",
            // assets-install info
            'assets-install [--symlink] [--relative] <target>' => 'Install assets in the given target',
            'The assets-install command will install assets in the given <target> directory. If no option given this command will copy assets into the target.',
            ['--symlink','This option will install assets using absolute symlink directory'],
            ['--relative','This option will install assets using relative symlink'],
            ""
        ];
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

        $sm->get('Tracy')->startDebug();
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
     * @param ModuleManager $manager
     */
    public function init(ModuleManager $manager)
    {
        $events = $manager->getEventManager();
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this,'onMergeConfig']);
    }

    /**
     * Manipulate configuration
     * @param ModuleEvent $event
     */
    public function onMergeConfig(ModuleEvent $event)
    {
        $listener = $event->getConfigListener();
        $config = $listener->getMergedConfig(false);

        // disable subsplit command if we not in main development
        if (
            isset($config['console'])
            && !$this->isInMainDevelopment()
            && isset($config['console']['router']['routes']['subsplit'])
        ) {
            unset($config['console']['router']['routes']['subsplit']);
        }

        $listener->setMergedConfig($config);
    }

    /**
     * Returns true if this module in the main development mode
     * @return bool
     */
    private function isInMainDevelopment()
    {
        return strpos(__DIR__, 'module/Core') !== false;
    }
}
