<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreTest\Factory\Service;

use Core\Factory\Service\HtmlPurifierFactory;
use Core\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class HtmlPurifierFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $cacheDir = sys_get_temp_dir().'/yawik/cache';
        $options = $this->createMock(ModuleOptions::class);
        $options->expects($this->once())
            ->method('getCacheDir')
            ->willReturn($cacheDir);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ModuleOptions::class)
            ->willReturn($options);

        $purifier = (new HtmlPurifierFactory())($container,'some-name');
        $config = $purifier->config->getAll();
        $this->assertEquals($cacheDir, $config['Cache']['SerializerPath']);
    }
}
