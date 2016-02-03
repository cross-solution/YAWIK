<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs;

use Zend\Mvc\MvcEvent;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Core\ModuleManager\ModuleConfigLoader;

/**
 * Bootstrap class of the Core module
 */
class Module implements ConsoleUsageProviderInterface
{

    public function getConsoleUsage(Console $console)
    {
        return array(
            'Manipulation of jobs database',
            'jobs expire [--filter=]' => 'expire jobs.',
            array('--filter=JSON', "available keys:\n"
                . "- 'days:INT' -> expire jobs after <days> days. Default 30\n"
                . "- 'limit':INT -> Limit jobs to expire per run. Default 10."),
        );
    }

    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/config');
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
                    __NAMESPACE__ . 'Test' => __DIR__ . '/test/' . __NAMESPACE__ . 'Test',
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $services     = $e->getApplication()->getServiceManager();
        $sharedManager = $eventManager->getSharedManager();

        $defaultlistener = $services->get('Jobs/Listener/Publisher');
        $defaultlistener->attachShared($sharedManager);
    }
}
