<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Admin;

use Zend\ModuleManager\Feature;
use Zend\Loader\StandardAutoloader;

/**
 * Bootstrap class of the Admin module
 * 
 */
/**
 * Bootstrap class of the Admin Module
 */
class Module implements Feature\DependencyIndicatorInterface,
                        Feature\AutoloaderProviderInterface,
                        Feature\ConfigProviderInterface
{
    public function getModuleDependencies()
    {
        return array('Core','Auth');
    }

    /**
     * indicates, that the autoload configuration for this module should be loaded.
     * @see
     *
     * @var bool
     */
    public static $isLoaded=false;


    function getConfig()
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
             'Zend\Loader\StandardAutoloader'  => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    __NAMESPACE__ . 'Test' => __DIR__ . '/test/' . __NAMESPACE__ . 'Test',
                ),
            ),
        );
    }

    function onBootstrap()
    {
        self::$isLoaded=true;
    }
}
