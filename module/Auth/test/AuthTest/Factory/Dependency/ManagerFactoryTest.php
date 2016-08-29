<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Factory\Dependency;

use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Factory\Dependency\ManagerFactory;
use Auth\Dependency\Manager;
use Zend\EventManager\EventManagerInterface as Events;

/**
 * @coversDefaultClass \Auth\Factory\Dependency\ManagerFactory
 */
class ManagerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $events = $this->getMockBuilder(Events::class)
            ->getMock();
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Auth/Dependency/Manager/Events'))
            ->willReturn($events);
        
        $managerFactory = new ManagerFactory();
        $manager = $managerFactory->createService($serviceLocator);
        
        $this->assertInstanceOf(Manager::class, $manager);
        $this->assertSame($events, $manager->getEventManager());
    }
}
