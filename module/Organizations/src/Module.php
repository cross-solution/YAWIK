<?php
/**
 * YAWIK
 * Organizations Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations;

use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use Core\ModuleManager\ModuleConfigLoader;
use Core\Options\ModuleOptions as CoreOptions;
use Yawik\Composer\RequireDirectoryPermissionInterface;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;

/**
 * Bootstrap class of the organizations module
 */
class Module implements
    BootstrapListenerInterface,
    DependencyIndicatorInterface,
    RequireDirectoryPermissionInterface,
    VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = \Core\Module::VERSION;
    /**
     * @param CoreOptions $options
     * @return array
     */
    public function getRequiredDirectoryLists(CoreOptions $options)
    {
        return [
            $options->getPublicDir().'/static/Organizations',
            $options->getPublicDir().'/static/Organizations/Image',
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


    public function onBootstrap(EventInterface $e)
    {
        /* @var $e MvcEvent */
        $eventManager = $e->getApplication()->getEventManager();
        $sharedManager = $eventManager->getSharedManager();

        $createJobListener = new \Organizations\Acl\Listener\CheckJobCreatePermissionListener();
        $createJobListener->attachShared($sharedManager);

        if ($e->getRequest() instanceof \Laminas\Http\Request) {
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) {
                $serviceManager = $event->getApplication()
                    ->getServiceManager();
                $options = $serviceManager->get('Organizations/ImageFileCacheOptions');

                if ($options->getEnabled()) {
                    $serviceManager->get('Organizations\ImageFileCache\ApplicationListener')
                        ->onDispatchError($event);
                }
            });
        }
    }

    public function getModuleDependencies()
    {
        return [
            'Core',
            'Auth',
        ];
    }
}
