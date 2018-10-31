<?php

/*
 * This file is part of the Yawik project.
 *
 *     (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest;

use Core\Yawik;
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * Class Bootstrap
 *
 * @TODO: Find a way to merge this class with Core\Bootstrap
 * @package CoreTest
 * @since 0.32
 */
class Bootstrap extends Yawik
{

    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    protected static $config;

    protected static $bootstrap;

    public static function loadConfig()
    {
        $configDir = getenv('APP_CONFIG_DIR');
        if (!$configDir) {
            $configDir = getcwd().'/config';
        }

        $config = include $configDir.'/config.php';
        if (is_file($file = $configDir.'/config.test.php')) {
            $config = ArrayUtils::merge($config, include $file);
        }
        return $config;
    }

    /**
     * Initialize test bootstrap
     */
    public static function init()
    {
        static $initialized = false;
        if (!$initialized) {
            parent::init();
            //date_default_timezone_set('Europe/Berlin');
            error_reporting(E_ALL | E_STRICT);
            $testConfig = static::loadConfig();
            static::$config = $testConfig;
            static::setupServiceManager();
            $initialized = true;
        }
    }

    /**
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        return static::$config;
    }

    public static function setupServiceManager()
    {
        $smConfig = new ServiceManagerConfig(static::$config);
        $serviceManager = new ServiceManager($smConfig->toArray());
        $serviceManager->setService('ApplicationConfig', static::$config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ?: (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new \RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        }
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ),
            ),
        ));
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

Bootstrap::init();
