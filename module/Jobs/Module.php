<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
            'jobs generatekeywords [--filter=]' => '(Re-)Generates keywords for all jobs.',
            array('--filter=JSON', "available keys:\n"
                . "- 'before:ISODate' -> only jobs before the given date\n"
                . "- 'after':ISODate' -> only jobs after the given date\n"
                . "- 'title':String -> exakt title to match or if starting with '/' -> MongoRegex\n"
                . "- 'limit':INT -> Limit result."),
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

