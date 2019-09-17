<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth;

use Acl\Listener\CheckPermissionsListener;
use Auth\Listener\SocialProfilesUnconfiguredErrorListener;
use Yawik\Composer\AssetProviderInterface;
use Core\ModuleManager\ModuleConfigLoader;
use Zend\Loader\Exception\InvalidArgumentException;
use Zend\Mvc\MvcEvent;
use Auth\Listener\TokenListener;
use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;

/**
 * Bootstrap class of the Auth module
 */
class Module implements AssetProviderInterface, VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = \Core\Module::VERSION;

    public function init(\Zend\ModuleManager\ModuleManagerInterface $moduleManager)
    {
        if (\Zend\Console\Console::isConsole()) {
            return;
        }

        $eventManager  = $moduleManager->getEventManager()->getSharedManager();
        $tokenListener = new TokenListener();
        $tokenListener->attachShared($eventManager);
    }
    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/../../config');
    }

    /**
     * Loads module specific autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        $addProvidersDir = null;
        $directories = [
            __DIR__.'/../../../../hybridauth/hybridauth/additional-providers',
            __DIR__.'/../../../../vendor/hybridauth/hybridauth/additional-providers',
        ];

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $addProvidersDir = $directory;
                break;
            }
        }

        if (is_null($addProvidersDir)) {
            throw new InvalidArgumentException('HybridAuth additional providers directories is not found.');
        }

        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                // This is an hack due to bad design of Hybridauth
                // This ensures the class from "addtional-providers" is loaded.
                array(
                    'Hybrid_Providers_XING' => $addProvidersDir . '/hybridauth-xing/Providers/XING.php',
                ),
                array(
                    'Hybrid_Providers_Github' => $addProvidersDir. '/hybridauth-github/Providers/GitHub.php',
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        if (\Zend\Console\Console::isConsole()) {
            return;
        }
        $eventManager = $e->getApplication()->getEventManager();
        $services     = $e->getApplication()->getServiceManager();

        $eventManager->attach(
            MvcEvent::EVENT_ROUTE,
            function (MvcEvent $e) use ($services) {
                /* @var $checkPermissionsListener \Acl\Listener\CheckPermissionsListener */
                $checkPermissionsListener = $services->get('Auth/CheckPermissionsListener');
                $checkPermissionsListener->onRoute($e);
            },
            -10
        );

        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH,
            function (MvcEvent $e) use ($services) {
                /** @var CheckPermissionsListener $checkPermissionsListener */
                $checkPermissionsListener = $services->get('Auth/CheckPermissionsListener');
                $checkPermissionsListener->onDispatch($e);
            },
            10
        );

        $unauthorizedAccessListener = $services->get('UnauthorizedAccessListener');
        $unauthorizedAccessListener->attach($eventManager);

        $deactivatedUserListener = $services->get('DeactivatedUserListener');
        $deactivatedUserListener->attach($eventManager);

        $socialProfilesUnconfiguredErrorListener = new SocialProfilesUnconfiguredErrorListener();
        $socialProfilesUnconfiguredErrorListener->attach($eventManager);
    }

    public function getPublicDir()
    {
        return realpath(__DIR__.'/../../public');
    }
}
