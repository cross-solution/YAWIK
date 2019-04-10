<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Factory\Dependency;

use PHPUnit\Framework\TestCase;

use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Factory\Dependency\ManagerFactory;
use Auth\Dependency\Manager;
use Zend\EventManager\EventManagerInterface as Events;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @coversDefaultClass \Auth\Factory\Dependency\ManagerFactory
 */
class ManagerFactoryTest extends TestCase
{

    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $events = $this->getMockBuilder(Events::class)
            ->getMock();
        
        $documentManager = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['Auth/Dependency/Manager/Events', $events],
                ['Core/DocumentManager', $documentManager]
            ]));
        
        $managerFactory = new ManagerFactory();
        $manager = $managerFactory->createService($serviceLocator);
        
        $this->assertInstanceOf(Manager::class, $manager);
        $this->assertSame($events, $manager->getEventManager());
    }
}
