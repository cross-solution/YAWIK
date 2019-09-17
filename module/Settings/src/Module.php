<?php
/**
 * YAWIK
 * Settings Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core */
namespace Settings;

use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use Zend\Mvc\MvcEvent;
use Settings\Listener\InjectSubNavigationListener;

/**
 * Bootstrap class of the Settings module
 *
 */
class Module implements VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = \Core\Module::VERSION;

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
        // we attach with wildcard events name
        $events = $e->getApplication()->getEventManager();
        $events->attach(
            MvcEvent::EVENT_RENDER,
            new InjectSubNavigationListener(),
            10
        );
    }

    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
