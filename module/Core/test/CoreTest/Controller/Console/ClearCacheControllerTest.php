<?php

/*
 * This file is part of the Yawik project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace CoreTest\Controller\Console;

use PHPUnit\Framework\TestCase;

use Core\Controller\Console\ClearCacheController;
use Core\Service\ClearCacheService;
use Interop\Container\ContainerInterface;
use Zend\Console\Adapter\AdapterInterface;

/**
 * Class CacheWarmupControllerTest
 * @covers \Core\Controller\Console\ClearCacheController
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.32.0
 * @package CoreTest\Controller\Console
 */
class ClearCacheControllerTest extends TestCase
{
    public function testFactory()
    {
        $cache = $this->getMockBuilder(ClearCacheService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ClearCacheService::class)
            ->willReturn($cache)
        ;

        ClearCacheController::factory($container);
    }

    public function testIndexAction()
    {
        $cache = $this->getMockBuilder(ClearCacheService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $console = $this->createMock(AdapterInterface::class);

        $cache->expects($this->once())
            ->method('clearCache')
        ;
        $console->expects($this->once())
            ->method('writeLine')
        ;

        $target = new ClearCacheController($cache);
        $target->setConsole($console);
        $target->indexAction();
    }
}
