<?php

/*
 * This file is part of the Yawik project.
 *
 *     (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core;

use Zend\Mvc\Application as ZendApplication;
use Zend\Stdlib\ArrayUtils;

/**
 * Application Class
 *
 * @package Core
 */
class Application
{
    /**
     * Get required modules for Yawik
     *
     * @return array
     */
    public static function getRequiredModules()
    {
        return array(
            'Zend\ServiceManager\Di',
            'Zend\Session',
            'Zend\Router',
            'Zend\Navigation',
            'Zend\I18n',
            'Zend\Filter',
            'Zend\InputFilter',
            'Zend\Form',
            'Zend\Validator',
            'Zend\Log',
            'Zend\Mvc\Plugin\Prg',
            'Zend\Mvc\Plugin\Identity',
            'Zend\Mvc\Plugin\FlashMessenger',
            'Zend\Mvc\I18n',
            'Zend\Mvc\Console',
            'Zend\Hydrator',
            'Zend\Serializer',
            'DoctrineModule',
            'DoctrineMongoODMModule',
        );
    }

    /**
     * Generate modules to be loaded for Yawik application
     *
     * @param array $loadModules
     * @return array
     */
    public static function generateModuleConfiguration($loadModules=[])
    {
        return array_merge(
            static::getRequiredModules(),
            $loadModules
        );
    }

    /**
     * Run application
     * @param $appConfig
     *
     * @return bool|ZendApplication
     */
    public static function run(array $appConfig = [])
    {
        ini_set('display_errors', true);
        ini_set('error_reporting', E_ALL | E_STRICT);

        date_default_timezone_set('Europe/Berlin');

        if (!version_compare(PHP_VERSION, '5.6.0', 'ge')) {
            echo sprintf('<p>Sorry, YAWIK requires at least PHP 5.6.0 to run, but this server currently provides PHP %s</p>', PHP_VERSION);
            echo '<p>Please ask your servers\' administrator to install the proper PHP version.</p>';
            exit;
        }

        if (php_sapi_name() == 'cli-server') {
            $parseUrl = parse_url(substr($_SERVER["REQUEST_URI"], 1));
            $route = isset($parseUrl['path']) ? $parseUrl['path']:null;
            if (is_file(__DIR__ . '/' . $route)) {
                if (substr($route, -4) == ".php") {
                    require __DIR__ . '/' . $route;     // Include requested script files
                    exit;
                }
                return false;           // Serve file as is
            } else {                    // Fallback to index.php
                $_GET["q"] = $route;    // Try to emulate the behaviour of a .htaccess here.
            }
        }

        if (empty($appConfig)) {
            // Retrieve configuration
            $appConfig = require __DIR__ . '/../config/application.config.php';
            if (file_exists(__DIR__ . '/../config/development.config.php')) {
                $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
            }
        }

        // Run the application!
        return ZendApplication::init($appConfig)->run();
    }
}
