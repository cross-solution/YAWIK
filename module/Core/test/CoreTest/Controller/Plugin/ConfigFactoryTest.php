<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Core\Controller\Plugin\Config;
use Core\Controller\Plugin\ConfigFactory;
use Interop\Container\ContainerInterface;

/**
 * Class ConfigFactoryTest
 *
 * @covers \Core\Controller\Plugin\ConfigFactory
 * @package CoreTest\Controller\Plugin
 */
class ConfigFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('Config')
            ->willReturn([])
        ;
        $factory = new ConfigFactory();
        $service = $factory($container, 'some');

        $this->assertInstanceOf(Config::class, $service);
    }
}
