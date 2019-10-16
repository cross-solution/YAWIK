<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest;

use PHPUnit\Framework\TestCase;

use Core\Application;
use org\bovigo\vfs\vfsStream;
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
 * @author  Anthonius Munthi <https://itstoni.com>
 * @since   0.32
 * @covers  \Core\Application
 * @package CoreTest
 */
class ApplicationTest extends TestCase
{
    private static $env;

    private static $configDir;

    private static $cwd;

    public static function setUpBeforeClass(): void
    {
        static::$env = getenv('APPLICATION_ENV');
        static::$configDir = Application::getConfigDir();
        static::$cwd = getcwd();
    }

    public static function tearDownAfterClass(): void
    {
        static::restore();
    }

    private static function restore()
    {
        putenv('APP_CONFIG_DIR='.static::$configDir);
        putenv('APPliCATION_ENV='.static::$env);
        chdir(static::$cwd);
    }

    protected function setUp(): void
    {
        TestApplication::emptyConfigDir();
    }

    protected function tearDown(): void
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

    public function testLoadDotEnv()
    {
        $tempDir    = sys_get_temp_dir().'/yawik/application-tests';
        $tempFile   = $tempDir.'/.env';
        $tempFileDist   = $tempDir.'/.env.dist';

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // .env contents
        $contents = <<<EOC
FOO="BAR"
OVERRIDE="OVERRIDED"
EOC;

        // .env.dist contents
        $contentsDist = <<<EOC
HELLO="WORLD"
OVERRIDE="NOT OVERRIDED"
EOC;
        @unlink($tempFile);
        @unlink($tempFileDist);

        $this->assertFalse(getenv('FOO'));
        // let the test begin ;-)
        chdir($tempDir); // setup dir first

        file_put_contents($tempFileDist, $contentsDist, LOCK_EX);
        TestApplication::loadDotEnv();
        $this->assertEquals('WORLD', getenv('HELLO'));
        $this->assertEquals('NOT OVERRIDED', getenv('OVERRIDE'));
        $this->assertEquals('Europe/Berlin', getenv('TIMEZONE'));


        file_put_contents($tempFile, $contents, LOCK_EX);
        TestApplication::loadDotEnv();
        $this->assertEquals('WORLD', getenv('HELLO'));
        $this->assertEquals("BAR", getenv('FOO'));
        $this->assertEquals("OVERRIDED", getenv("OVERRIDE"));
    }

    public function testLoadConfigThrowsWhenConfigFileNotFound()
    {
        chdir($tmpDir = sys_get_temp_dir());
        $this->assertEquals($tmpDir, getcwd());
        TestApplication::emptyConfigDir();
        TestApplication::loadConfig();
    }
}
