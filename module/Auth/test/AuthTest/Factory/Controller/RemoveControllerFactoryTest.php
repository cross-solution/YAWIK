<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Factory\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Auth\Factory\Controller\RemoveControllerFactory;
use Auth\Controller\RemoveController;
use Auth\Dependency\Manager;
use Auth\AuthenticationService;

/**
 * @coversDefaultClass \Auth\Factory\Controller\RemoveControllerFactory
 */
class RemoveControllerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['Auth/Dependency/Manager', $manager],
                ['AuthenticationService', $authService]
            ]));
        
        $controllerManager = $this->getMockBuilder(ControllerManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $controllerManager->expects($this->once())
            ->method('getServiceLocator')
            ->willReturn($serviceLocator);
        
        $controllerFactory = new RemoveControllerFactory();
        $this->assertInstanceOf(RemoveController::class, $controllerFactory->createService($controllerManager));
    }
}
