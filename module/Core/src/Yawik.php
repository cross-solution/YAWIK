<?php

/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution <http://cross-solution.de>
 * @license MIT
 */


namespace Core;

use Symfony\Component\Dotenv\Dotenv;
use Zend\Mvc\Application as ZendApplication;
use Zend\Stdlib\ArrayUtils;

/**
 * Utility class
 *
 * @package Core
 * @since 0.32
 */
class Yawik
{
    public static $VERSION;

    public static function init()
    {
        $env = getcwd().'/.env';
        if (!is_file($env)) {
            $env = getcwd().'/.env.dist';
        }
        if (!is_file($env)) {
            return;
        }
        $dotenv = new Dotenv();
        $dotenv->load($env);

        $version = getenv('TRAVIS') ? "undefined":exec('git describe');
        $branch = getenv('TRAVIS') ? "undefined":exec('git rev-parse --abbrev-ref HEAD', $output, $retVal);
        static::$VERSION = $version.'['.$branch.']';
    }

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
        return ArrayUtils::merge(
            static::getRequiredModules(),
            $loadModules
        );
    }

    /**
     * Run application
     * @param array $appConfig
     *
     * @param bool $run
     * @return bool|ZendApplication
     */
    public static function initApplication(array $appConfig = [])
    {
        static::init();
        if (empty($appConfig)) {
            // Retrieve configuration
            $file = null;
            if (is_file($test = getcwd().'/test/config/config.php')) {
                $file = $test;
            } elseif (is_file($test = getcwd(). '/config/config.php')) {
                $file = $test;
            } elseif (is_file($test = __DIR__.'/../config/config.php')) {
                $file = $test;
            } elseif (is_file($test = __DIR__.'/../../../../config/config.php')) {
                $file = $test;
            } else {
                fwrite(
                    STDERR,
                    'You must set up the project dependencies, run the following commands:'.PHP_EOL.
                    'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
                    'php composer.phar install'.PHP_EOL
                );
                exit(1);
            }
            $appConfig = include $file;
        }
        return ZendApplication::init($appConfig);
    }

    public static function runApplication(array $appConfig = [])
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
        return static::initApplication($appConfig)->run();
    }
}

Yawik::init();
