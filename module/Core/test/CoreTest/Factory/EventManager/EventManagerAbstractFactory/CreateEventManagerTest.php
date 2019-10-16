<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\EventManager\EventManagerAbstractFactory;

use PHPUnit\Framework\TestCase;

use Core\Factory\EventManager\EventManagerAbstractFactory;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Factory\EventManager\EventManagerAbstractFactory::createEventManager
 *
 * @covers \Core\Factory\EventManager\EventManagerAbstractFactory::createEventManager
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.EventManager
 * @group Core.Factory.EventManager.EventManagerAbstractFactory
 */
class CreateEventManagerTest extends TestCase
{

    /**
     *
     *
     * @var EventManagerAbstractFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = $this->getMockBuilder('\Core\Factory\EventManager\EventManagerAbstractFactory')
                             ->setMethods([ 'attachListeners', 'getConfig' ])
                             ->getMock();
    }

    protected function setTargetConfig($config)
    {
        if (!isset($config['service'])) {
            $config['service'] = 'Test/Events/Manager';
        }
        if (!isset($config['listeners'])) {
            $config['listeners'] = [];
        }
        if (!isset($config['identifiers'])) {
            $config['identifiers'] = [ 'Test/Events/Manager' ];
        }
        if (!isset($config['event'])) {
            $config['event'] = '\Zend\EventManager\Event';
        }
        $this->target
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($config)
        ;
    }

    protected function getServiceManagerMock($events, $args = [])
    {
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();


        if (is_Array($events)) {
            $keys = array_keys($events);
            $eventsService = $keys[0];
            $events = $events[$eventsService];
        } else {
            $eventsService = 'Test/Events/Manager';
        }

        $hasMap = [ [ $eventsService, true ] ];
        $getMap = [ [ $eventsService, $events ] ];

        foreach ($args as $serviceName => $serviceValue) {
            if (false === $serviceValue) {
                $hasMap[] = [ $serviceName, true ];
            } else {
                $hasMap[] = [ $serviceName, true];
                $getMap[] = [ $serviceName, $serviceValue ];
            }
        }

        /*$services->expects($this->exactly(count($hasMap)))
                 ->method('has')->will($this->returnValueMap($hasMap));
        */
        $services->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($hasMap))
        ;
        $services->expects($this->exactly(count($getMap)))
                 ->method('get')->will($this->returnValueMap($getMap));

        return $services;
    }

    public function testPullsInstanceFromServiceManager()
    {
        $events = new EventManager();
        $services = new ServiceManager();

        $services->setService('Test/Events/Manager', $events);

        $this->setTargetConfig([ 'service' => 'Test/Events/Manager', 'configure' => false, 'listeners' => []]);

        $this->target->expects($this->once())->method('attachListeners')->with($services, $events, []);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }

    public function testInstatiatesEventManagerFromClassName()
    {
        $this->setTargetConfig([ 'service' => '\Zend\EventManager\EventManager', 'configure' => false, 'listeners' => []]);

        $services = new ServiceManager();
        $events = $this->target->createServiceWithName($services, 'irrelevant', 'irrelevant');

        $this->assertInstanceOf('\Zend\EventManager\EventManager', $events);
    }

    public function testThrowsExceptionIfNoEventManagerCanBeCreated()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Cannot create');

        $this->setTargetConfig(['service' => 'NonExistantClass' ]);

        $this->target->createServiceWithName(new ServiceManager(), 'irrelevant', 'irrelevant');
    }

    public function testDoesNotConfigureEventManagerIfFlagIsFalse()
    {
        $services = $this->getServiceManagerMock(new EventManager());
        $this->setTargetConfig(['service' => 'Test/Events/Manager', 'configure' => false]);

        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }

    public function testSetsIdentifiersOnEventManagerInstance()
    {
        $ids = [ 'testID', 'TestId2' ];
        $events = new EventManager();
        $services = $this->getServiceManagerMock(['EventManager' => $events]);
        $this->setTargetConfig(['configure' => true, 'identifiers' => $ids, 'service' => 'EventManager']);

        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');

        $this->assertEquals($ids, $events->getIdentifiers());
    }

    public function testSetsEventPrototypeOnEventProviderEventManagers()
    {
        $event = new \Zend\EventManager\Event();
        $events = $this->getMockBuilder('\Core\EventManager\EventManager')->disableOriginalConstructor()
                        ->setMethods(['setIdentifiers', 'setEventPrototype'])->getMock();
        $events
            ->expects($this->once())
            ->method('setEventPrototype')
            ->with($event)
        ;

        $services = $this->getServiceManagerMock(['EventManager' => $events], [ 'Event' => $event ]);
        $this->setTargetConfig(['configure' => true, 'service' => 'EventManager', 'event' => 'Event']);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }

    public function testSetsEventClassOnNonEventProviderEventManagers()
    {
        $event = new \Zend\EventManager\Event();
        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                       ->disableOriginalConstructor()
                       ->setMethods(['setIdentifiers', 'setEventPrototype'])
                       ->getMock()
        ;
        $events
            ->expects($this->once())
            ->method('setEventPrototype')
            ->with($this->isInstanceOf(Event::class))
        ;

        $services = $this->getServiceManagerMock(['EventManager' => $events]);
        $this->setTargetConfig(['configure' => true, 'service' => 'EventManager', 'event' => '\Zend\EventManager\Event']);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }

    public function testSetsSharedEventManager()
    {
        $sharedEvents = new SharedEventManager();
        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')->disableOriginalConstructor()
                        ->setMethods(['setIdentifiers', 'setEventClass', 'setSharedManager'])->getMock();
        $events->expects($this->once())->method('setSharedManager')->with($sharedEvents);

        $services = $this->getServiceManagerMock($events, ['SharedEventManager' => $sharedEvents]);
        $this->setTargetConfig(['configure' => true]);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }
}
