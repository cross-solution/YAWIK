<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest;

use Core\Application;
use Zend\Mvc\Application as ZendApplication;

class TestApplication extends Application
{
    protected static $configDir;

    public static function emptyConfigDir()
    {
        static::$configDir = null;
    }
}

/**
 * Class ApplicationTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.32
 * @covers  \Core\Application
 * @package CoreTest
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    private static $env;

    private static $configDir;

    private static $cwd;

    public static function setUpBeforeClass()
    {
        static::$env = getenv('APPLICATION_ENV');
        static::$configDir = Application::getConfigDir();
        static::$cwd = getcwd();
    }

    public static function tearDownAfterClass()
    {
        static::restore();
    }

    private static function restore()
    {
        putenv('APP_CONFIG_DIR='.static::$configDir);
        putenv('APPliCATION_ENV='.static::$env);
        chdir(static::$cwd);
    }

    public function setUp()
    {
        TestApplication::emptyConfigDir();
    }

    public function tearDown()
    {
        static::restore();
    }

    public function testLoadConfig()
    {
        $config = TestApplication::loadConfig();
        $options = $config['module_listener_options'];
        $this->assertArrayHasKey('module_listener_options', $config);
        $this->assertFalse($options['config_cache_enabled']);
    }

    public function testInit()
    {
        $app = TestApplication::init();
        $this->assertInstanceOf(ZendApplication::class, $app);
    }
}
