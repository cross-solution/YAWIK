<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2018 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core;

use Core\Service\ClearCacheService;
use Symfony\Component\Dotenv\Dotenv;
use Zend\Config\Exception\InvalidArgumentException;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\Mvc\Application as BaseApplication;
use Zend\Stdlib\ArrayUtils;
use SebastianBergmann\Version;

/**
 * Yawik Custom MVC Application
 *
 * @package Core
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.32
 */
class Application extends BaseApplication
{
    /**
     * Current yawik revision
     * @var string
     */
    public static $revision;

    /**
     * A short version of package
     * @var string
     */
    public static $version;

    /**
     * Current yawik environment
     * @var string
     */
    public static $env;

    /**
     * Current yawik config directory
     * @var string
     */
    private static $configDir;

    /**
     * @return string
     */
    public static function getCompleteVersion()
    {
        return sprintf('%s@%s', static::$version, static::$revision);
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
        $modules = ArrayUtils::merge(
            static::getRequiredModules(),
            $loadModules
        );
        return $modules;
    }

    /**
     * Get config directory location
     *
     * @return string Configuration directory
     */
    public static function getConfigDir()
    {
        if (is_null(static::$configDir)) {
            $configDir = '';
            $dirs = [
                // path/to/module/test/sandbox/config directories
                __DIR__.'/../../../../*/sandbox/config',

                // path/to/yawik-standard/config
                __DIR__.'/../../../config',
            ];
            foreach ($dirs as $dir) {
                foreach (glob($dir) as $testDir) {
                    $configDir = realpath($testDir);
                    break;
                }
                if (is_dir($configDir)) {
                    break;
                }
            }

            if (!is_dir($configDir)) {
                throw new InvalidArgumentException('Can not determine which config directory to be used.');
            }

            static::$configDir = $configDir;
        }
        return static::$configDir;
    }

    /**
     * @inheritdoc
     */
    public static function init($configuration = [])
    {
        // @codeCoverageIgnoreStart
        if (!version_compare(PHP_VERSION, '7.1.0', 'ge')) {
            echo sprintf('<p>Sorry, YAWIK requires at least PHP 7.1.0 to run, but this server currently provides PHP %s</p>', PHP_VERSION);
            echo '<p>Please ask your servers\' administrator to install the proper PHP version.</p>';
            exit;
        }
        // @codeCoverageIgnoreEnd

        ini_set('display_errors', true);
        ini_set('error_reporting', E_ALL | E_STRICT);

        static::loadDotEnv();

        if (isset($configuration['config_dir'])) {
            static::$configDir = $configuration['config_dir'];
        }
        static::generateVersion();
        $configuration = static::loadConfig($configuration);
        static::checkCache($configuration);
        return parent::init($configuration);
    }

    /**
     * Check current cache status
     * @param array $configuration
     */
    private static function checkCache(array $configuration)
    {
        $config = $configuration['module_listener_options'];
        $options = new ListenerOptions($config);
        $cache = new ClearCacheService($options);
        $cache->checkCache();
    }

    /**
     * Setup php server
     * @return bool
     * @codeCoverageIgnore
     */
    public static function setupCliServerEnv()
    {
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
        return true;
    }

    /**
     * Load environment variables from .env files
     */
    public static function loadDotEnv()
    {
        $dotenv = new Dotenv();
        if (is_file(getcwd().'/.env.dist')) {
            $dotenv->load(getcwd().'/.env.dist');
        }
        if (is_file($file = getcwd().'/.env')) {
            $dotenv->load($file);
        }

        if (false === getenv('TIMEZONE')) {
            putenv('TIMEZONE=Europe/Berlin');
        }
        date_default_timezone_set(getenv('TIMEZONE'));
    }

    /**
     * Load Application configuration
     * @param array $configuration
     * @return array
     */
    public static function loadConfig($configuration = [])
    {
        $configDir = static::getConfigDir();
        if (empty($configuration)) {
            $configFile = $configDir.'/config.php';
            // @codeCoverageIgnoreStart
            if (!is_file($configFile)) {
                throw new InvalidArgumentException(sprintf(
                    'Can not load config file "%s". Please be sure that this file exists and readable',
                    $configFile
                ));
            }
            // @codeCoverageIgnoreEnd
            $configuration = include $configFile;
        }


        $isCli = php_sapi_name() === 'cli';

        // load modules
        $modules = $configuration['modules'];
        $modules = static::generateModuleConfiguration($modules);

        $yawikConfig = $configDir.'/autoload/yawik.config.global.php';
        $installMode = false;
        if (!$isCli && !file_exists($yawikConfig)) {
            $modules = static::generateModuleConfiguration(['Install']);
            $installMode = true;
        } elseif (in_array('Install', $modules)) {
            $modules = array_diff($modules, ['Install']);
        }

        static::$env = $env = getenv('APPLICATION_ENV') ?: 'production';
        $defaults = [
            'module_listener_options' => [
                'module_paths' => [
                    './module',
                    './vendor',
                    './modules'
                ],
                // What configuration files should be autoloaded
                'config_glob_paths' => [
                    sprintf($configDir.'/autoload/{,*.}{global,%s,local}.php', $env)
                ],

                // Use the $env value to determine the state of the flag
                // caching disabled during install mode
                'config_cache_enabled' => ($env == 'production'),


                // Use the $env value to determine the state of the flag
                'module_map_cache_enabled' => ($env == 'production'),

                'module_map_cache_key' => 'module_map',

                // Use the $env value to determine the state of the flag
                'check_dependencies' => ($env != 'production'),

                'cache_dir' => getcwd()."/var/cache",
            ],
        ];

        $envConfig = [];
        $envConfigFile = $configDir.'/config.'.$env.'.php';
        if (file_exists($envConfigFile)) {
            if (is_readable($envConfigFile)) {
                $envConfig = include $envConfigFile;
            } else {
                \trigger_error(
                    sprintf('Environment config file "%s" is not readable.', $envConfigFile),
                    E_USER_NOTICE
                );
            }
        }

        // configuration file always win
        $configuration = ArrayUtils::merge($defaults, $configuration);
        // environment config always win
        $configuration = ArrayUtils::merge($configuration, $envConfig);

        $configuration['modules'] = $modules;

        // force disabled cache when in install mode
        if ($installMode) {
            $configuration['module_listener_options']['config_cache_enabled'] = false;
            $configuration['module_listener_options']['module_map_cache_enabled'] = false;
        }

        // setup docker environment
        if (getenv('DOCKER_ENV')=='yes') {
            $configuration = ArrayUtils::merge($configuration, static::getDockerEnv($configuration));
        }
        return $configuration;
    }

    /**
     * Override configuration in docker environment
     * This will fix filesystem writing during behat tests
     * @param $configuration
     * @return array
     */
    private static function getDockerEnv($configuration)
    {
        // add doctrine hydrator
        $cacheDir = $configuration['module_listener_options']['cache_dir'].'/docker';
        $configDir = static::getConfigDir();
        $hydratorDir = $cacheDir.'/Doctrine/Hydrator';
        $proxyDir = $cacheDir.'/Doctrine/Proxy';
        if (!is_dir($hydratorDir)) {
            mkdir($hydratorDir, 0777, true);
        }
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir, 0777, true);
        }
        return [
            'module_listener_options' => [
                'cache_dir' => $cacheDir,
                'config_glob_paths' => [
                    $configDir.'/autoload/*.docker.php',
                ]
            ],
            'doctrine' => [
                'configuration' => [
                    'odm_default' => [
                        'hydrator_dir' => $hydratorDir,
                        'proxy_dir' => $proxyDir,
                    ]
                ]
            ]
        ];
    }

    private static function generateVersion()
    {
        if (is_null(static::$revision)) {
            $dirs = [
                // in vendors or modules directory
                __DIR__.'/../.git',

                // in development mode
                __DIR__.'/../../../.git',
            ];

            $path = realpath(dirname(__DIR__));

            foreach ($dirs as $dir) {
                if (is_dir($dir)) {
                    $path = dirname(realpath($dir));
                    break;
                }
            }

            $info = new Version(Module::VERSION, $path);
            $exp  = explode("-g", $info->getVersion(), 2);

            static::$version  = $exp[0];
            static::$revision = isset($exp[1]) ? $exp[1] : '';
        }
    }
}
