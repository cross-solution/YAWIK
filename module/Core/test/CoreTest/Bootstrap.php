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

use PHPUnit\Framework\TestCase;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Core\Application;

/**
 * Class Bootstrap
 * @package CoreTest
 * @since 0.32
 */
class Bootstrap
{

    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    /**
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        if (!static::$serviceManager instanceof ServiceManager) {
            static::setupServiceManager();
        }
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        static $config;
        if (empty($config)) {
            $config = Application::loadConfig();
        }
        return $config;
    }

    public static function setupServiceManager()
    {
        $smConfig = new ServiceManagerConfig(static::getConfig());
        $serviceManager = new ServiceManager($smConfig->toArray());
        $serviceManager->setService('ApplicationConfig', static::getConfig());
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }
}
