<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Factory\Controller;

use Core\Controller\AdminController;
use Core\EventManager\EventManager;
use Core\Factory\Controller\AdminControllerFactory;
use Interop\Container\ContainerInterface;

class AdminControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $eventManager = $this->createMock(EventManager::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('Core/AdminController/Events')
            ->willReturn($eventManager)
        ;

        $factory = new AdminControllerFactory();
        $this->assertInstanceOf(
            AdminController::class,
            $factory($container,'Core/Admin')
        );
    }
}
