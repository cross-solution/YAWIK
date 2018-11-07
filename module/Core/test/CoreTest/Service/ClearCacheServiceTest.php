<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Service;

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
class ClearCacheServiceTest extends \PHPUnit_Framework_TestCase
{
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

    public function testClearCache()
    {
        $cacheDir = sys_get_temp_dir().'/yawik/test-cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $options = $this->getMockBuilder(ListenerOptions::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $options->expects($this->exactly(3))
            ->method('getCacheDir')
            ->willReturnOnConsecutiveCalls(
                null,
                sys_get_temp_dir().'/foo',
                $cacheDir
            )
        ;
        $fs = $this->createMock(Filesystem::class);
        $fs->expects($this->once())
            ->method('remove')
        ;


        $service = new ClearCacheService($options, $fs);
        $this->assertFalse($service->clearCache(), 'Test with null cache directory');
        $this->assertFalse($service->clearCache(), 'Test with non existent directory');
        $this->assertTrue($service->clearCache(), 'Test with exist cache directory');
    }
}
