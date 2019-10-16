<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Service;

use PHPUnit\Framework\TestCase;

use Core\Application;
use Core\Service\ClearCacheService;
use CoreTest\Bootstrap;
use Interop\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class ClearCacheServiceTest
 *
 * @author      Anthonius Munthi <me@itstoni.com>
 * @since       0.32
 * @package     CoreTest\Service
 */
class ClearCacheServiceTest extends TestCase
{
    private static $testCacheDir;

    public static function setUpBeforeClass(): void
    {
        $cacheDir = sys_get_temp_dir().'/yawik/test-cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        touch($cacheDir.'/.checksum', 0777);
        static::$testCacheDir = $cacheDir;
    }


    protected function setUp(): void
    {
        static::setUpBeforeClass();
    }

    public function testFactory()
    {
        $config = [
            'module_listener_options' => []
        ];
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('ApplicationConfig')
            ->willReturn($config)
        ;
        $cache = ClearCacheService::factory($container);
        $this->assertInstanceOf(ClearCacheService::class, $cache);
    }

    public function testCheckCache()
    {
        $cacheDir = sys_get_temp_dir().'/yawik/test-cache';
        $checkSumFile = $cacheDir.'/.checksum';
        if (is_file($checkSumFile)) {
            unlink($checkSumFile);
        }
        $config = Bootstrap::getConfig();
        $config = ArrayUtils::merge($config, [
            'module_listener_options' => [
                'cache_dir' => $cacheDir
            ]
        ]);

        $options = new ListenerOptions($config['module_listener_options']);

        $fs = $this->createMock(Filesystem::class);
        $fs->expects($this->once())
            ->method('remove')
        ;
        $cache = new ClearCacheService($options, $fs);
        $cache->checkCache();
        $this->assertDirectoryExists($cacheDir);
        $this->assertFileExists($cacheDir.'/.checksum');
    }

    /**
     * @param string        $cacheDir
     * @param string|bool   $expectThrow
     * @param string        $message
     * @throws \Exception
     * @dataProvider    getTestClearCache
     */
    public function testClearCache($cacheDir, $expectThrow=false, $message='Test Cache Directory')
    {
        $options = $this->getMockBuilder(ListenerOptions::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $options->expects($this->any())
            ->method('getCacheDir')
            ->willReturn($cacheDir)
        ;

        if ($expectThrow) {
            $this->expectException(\Exception::class);
        }
        $service = new ClearCacheService($options, new Filesystem());
        $service->clearCache();
        $this->assertFileNotExists($cacheDir.'/.checksum', $message);
    }

    public function getTestClearCache()
    {
        $cacheDir = sys_get_temp_dir().'/yawik/test-cache';
        return [
            [null,true,'Test with null cache directory'],
            [sys_get_temp_dir().'/foo',true,'Test with non existent directory'],
            [$cacheDir,false,'Test with valid cache directory']
        ];
    }

    /**
     * Clear cache depth only allowed to first level,
     * not into sub directory
     */
    public function testClearCacheDepth()
    {
        $cacheDir = static::$testCacheDir;
        @mkdir($cacheDir.'/foo', 0777, true);
        @mkdir($cacheDir.'/hello', 0777, true);
        touch($cacheDir.'/foo/bar.php', 0777, true);
        touch($cacheDir.'/hello/world.php', 0777, true);
        touch($cacheDir.'/.checksum', 0777, true);

        $options = $this->getMockBuilder(ListenerOptions::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $options->expects($this->once())
            ->method('getCacheDir')
            ->willReturn($cacheDir)
        ;
        $fs = new Filesystem();
        clearstatcache();
        $service = new ClearCacheService($options, $fs);
        $this->assertTrue($service->clearCache());
        $this->assertFileNotExists($cacheDir.'/.checksum');
        $this->assertFileExists($cacheDir.'/hello/world.php');
        $this->assertFileExists($cacheDir.'/foo/bar.php');
    }
}
