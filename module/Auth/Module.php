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
use Zend\Mvc\MvcEvent;
use Auth\View\InjectLoginInfoListener;
use Auth\Listener\TokenListener;
use Auth\Listener\UnauthorizedAccessListener;

/**
 * Bootstrap class of the Core module
 *
 */
class Module
{

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
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Loads module specific autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {

        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                // This is an hack due to bad design of Hybridauth
                // This ensures the class from "addtional-providers" is loaded.
                array(
                    'Hybrid_Providers_XING'
                    => __DIR__ . '/../../vendor/hybridauth/hybridauth/additional-providers/hybridauth-xing/Providers/XING.php',
                ),
                array(
                    'Hybrid_Providers_Github'
                    => __DIR__ . '/../../vendor/hybridauth/hybridauth/additional-providers/hybridauth-github/Providers/GitHub.php',
                ),
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'AuthTest' => __DIR__ . '/test/AuthTest',
                    'Acl' => __DIR__ . '/src/Acl',
                    'AclTest' => __DIR__ . '/test/AclTest',
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
            /** @var CheckPermissionsListener $checkPermissionsListener */
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

        $sharedManager = $eventManager->getSharedManager();
        $defaultlistener = $services->get('Auth/Listener/AuthAggregateListener');
        $defaultlistener->attachShared($sharedManager);

        $socialProfilesUnconfiguredErrorListener = new SocialProfilesUnconfiguredErrorListener();
        $socialProfilesUnconfiguredErrorListener->attach($eventManager);

    }
}
