<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs;

use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use Zend\Mvc\MvcEvent;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Core\ModuleManager\ModuleConfigLoader;
use Yawik\Composer\RequireDirectoryPermissionInterface;
use Core\Options\ModuleOptions as CoreOptions;

/**
 * Bootstrap class of the Core module
 */
class Module implements
    ConsoleUsageProviderInterface,
    RequireDirectoryPermissionInterface,
    VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = \Core\Module::VERSION;

    public function getRequiredDirectoryLists(CoreOptions $options)
    {
        return [
            $options->getPublicDir().'/static/Jobs',
            $options->getPublicDir().'/static/Jobs/logos',
        ];
    }

    public function getConsoleUsage(Console $console)
    {
        return [
            'Expire jobs',
            'jobs expire [--days] [--limit] [--info]'  => 'Expire jobs',
            ['--days=INT', 'expire jobs after <days> days. Default 30'],
            ['--limit=INT[,<offset>]', 'Limit jobs to expire per run starting from <offset>. Default 10. 0 means no limit'],
            ['--info', 'Does not manipulate the database, but prints a list of all matched jobs.']
        ];
    }

    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/../config');
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
