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
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\SharedEventManager;
use Laminas\ServiceManager\ServiceManager;

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
            $config['event'] = '\Laminas\EventManager\Event';
        }
        $this->target
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($config)
        ;
    }

    protected function getServiceManagerMock($events, $args = [])
    {
        $services = $this->getMockBuilder('\Laminas\ServiceManager\ServiceManager')
                         ->setMethods(['has', 'get', 'build'])
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
        $buildMap = [ [ $eventsService, null, $events ] ];

        foreach ($args as $serviceName => $serviceValue) {
            if (false === $serviceValue) {
                $hasMap[] = [ $serviceName, true ];
            } else {
                if (is_array($serviceValue)) {
                    $serviceOptions = $serviceValu1[1] ?? null;
                    $serviceValue = $serviceValue[0];
                } else {
                    $serviceOptions = null;
                }
                $hasMap[] = [ $serviceName, true];
                $getMap[] = [ $serviceName, $serviceValue ];
                $buildMap[] = [ $serviceName, $serviceOptions, $serviceValue ];
            }
        }

        /*$services->expects($this->exactly(count($hasMap)))
                 ->method('has')->will($this->returnValueMap($hasMap));
        */

        $services->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($hasMap))
        ;
        $services->expects($this->atMost(count($getMap)))
                ->method('get')->will($this->returnValueMap($getMap));

        $services->expects($this->atMost(count($getMap)))
                ->method('build')->will($this->returnValueMap($buildMap));

        return $services;
    }

    public function testPullsInstanceFromServiceManager()
    {
        $events = new EventManager();
        $services = new ServiceManager();

        $factory = new class ($events) {
            public function __construct($events) {
                $this->events = $events;
            }

            public function __invoke()
            {
                return $this->events;
            }
        };

        $services->setFactory('Test/Events/Manager', $factory);

        $this->setTargetConfig([ 'service' => 'Test/Events/Manager', 'configure' => false, 'listeners' => []]);

        $this->target->expects($this->once())->method('attachListeners')->with($services, $events, []);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }

    public function testInstatiatesEventManagerFromClassName()
    {
        $this->setTargetConfig([ 'service' => '\Laminas\EventManager\EventManager', 'configure' => false, 'listeners' => []]);

        $services = new ServiceManager();
        $events = $this->target->createServiceWithName($services, 'irrelevant', 'irrelevant');

        $this->assertInstanceOf('\Laminas\EventManager\EventManager', $events);
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
        $event = new \Laminas\EventManager\Event();
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
        $event = new \Laminas\EventManager\Event();
        $events = $this->getMockBuilder('\Laminas\EventManager\EventManager')
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
        $this->setTargetConfig(['configure' => true, 'service' => 'EventManager', 'event' => '\Laminas\EventManager\Event']);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }

    public function testSetsSharedEventManager()
    {
        $sharedEvents = new SharedEventManager();
        $events = $this->getMockBuilder('\Laminas\EventManager\EventManager')->disableOriginalConstructor()
                        ->setMethods(['setIdentifiers', 'setEventClass', 'setSharedManager'])->getMock();
        $events->expects($this->once())->method('setSharedManager')->with($sharedEvents);

        $services = $this->getServiceManagerMock($events, ['SharedEventManager' => $sharedEvents]);
        $this->setTargetConfig(['configure' => true]);
        $this->target->createServiceWithName($services, 'irrelevant', 'Test/Events');
    }
}
