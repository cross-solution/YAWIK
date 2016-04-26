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

use Core\Factory\EventManager\EventManagerAbstractFactory;


/**
 * Tests for \Core\Factory\EventManager\EventManagerAbstractFactory::attachListeners
 * and       \Core\Factory\EventManager\EventManagerAbstractFactory::normalizeListenerOptions
 * 
 * @covers \Core\Factory\EventManager\EventManagerAbstractFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.EventManager
 * @group Core.Factory.EventManager.EventManagerAbstractFactory
 */
class AttachListenersTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     *
     * @var EventManagerAbstractFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $target;

    protected $events;

    protected $lazyAggregateMock;

    public function setUp()
    {
        $this->target = $this->getMockBuilder('\Core\Factory\EventManager\EventManagerAbstractFactory')
                             ->setMethods([ 'createEventManager', 'getConfig' ])
                             ->getMock();

        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                       ->disableOriginalConstructor()
                       ->setMethods(['attach'])
                       ->getMock();

        $this->target->expects($this->once())->method('createEventManager')->willReturn($events);
        $this->events = $events;

    }

    protected function setTargetListenerConfig($config)
    {
        $cfg = [ 'listeners' => $config ];
        $this->target->expects($this->once())->method('getConfig')->willReturn($cfg);
    }

    protected function getServiceManagerMock($listeners, $expectLazyListeners = false)
    {
        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();


        $hasMap = [ ];
        $getMap = [ ];
        $defaultListenerMock = new \stdClass();

        $lazyAggregateMock = $this->getLazyAggregateMock($expectLazyListeners);
        if ($expectLazyListeners) {
            $lazyAggregateMock->expects($this->once())->method('attach');
            $getMap[] = [ 'Core/Listener/DeferredListenerAggregate', true, $lazyAggregateMock ];
        }

        foreach ($listeners as $serviceName => $listenerMock) {
            if (is_int($serviceName)) {
                $serviceName = $listenerMock;
                $listenerMock = $defaultListenerMock;
            }

            if (false === $listenerMock) {
                $hasMap[] = [ $serviceName, true, true, false ];
            } else {
                $hasMap[] = [ $serviceName, true, true, true ];
                $getMap[] = [ $serviceName, true, $listenerMock ];
            }
        }

        $services->expects($this->exactly(count($hasMap)))
                 ->method('has')->will($this->returnValueMap($hasMap));

        $services->expects($this->exactly(count($getMap)))
                 ->method('get')->will($this->returnValueMap($getMap));

        return $services;
    }

    protected function getLazyAggregateMock($configureSetHooks = null)
    {
        if (!$this->lazyAggregateMock) {
            $this->lazyAggregateMock = $this->getMockBuilder('\Core\Listener\DeferredListenerAggregate')
                                       ->getMock();
            if (null !== $configureSetHooks) {
                if ($configureSetHooks) {
                    $this->lazyAggregateMock->expects($this->once())->method('setListeners')->willReturnSelf();
                } else {
                    $this->lazyAggregateMock->expects($this->never())->method('setListeners');
                }
            }
        }

        return $this->lazyAggregateMock;
    }

    public function testLazyListenersAreAttachedToDeferredListenerAggregate()
    {
        $this->setTargetListenerConfig(['TestListener' => ['test-event', true]]);
        $services = $this->getServiceManagerMock([], true);

        $this->target->createServiceWithName($services, 'irrlelevant', 'irrelevant');
    }

    public function testPullsListenersFromServiceManager()
    {
        $this->setTargetListenerConfig(['TestListener' => 'test-event']);
        $services = $this->getServiceManagerMock(['TestListener']);

        $this->target->createServiceWithName($services, 'irrelevant', 'irrelevant');
    }

    public function testCreatesListenersInstances()
    {
        $this->setTargetListenerConfig(['\CoreTest\Factory\EventManager\EventManagerAbstractFactory\AttachListenerTestListenerMock' => 'test-event']);
        $services = $this->getServiceManagerMock(['\CoreTest\Factory\EventManager\EventManagerAbstractFactory\AttachListenerTestListenerMock' => false]);

        $this->target->createServiceWithName($services, 'irrelevant', 'irrlelevant');
    }

    public function testThrowsExceptionIfListenerCannotBeFetchedOrCreated()
    {
        $this->setTargetListenerConfig(['NonExistantClass' => 'event']);
        $services = $this->getServiceManagerMock(['NonExistantClass' => false]);

        $this->setExpectedException('\UnexpectedValueException', 'Cannot create listener');

        $this->target->createServiceWithName($services, 'irr', 'relevant');
    }

    public function testCallsAttachOnListenerAggregates()
    {
        $aggregateMock = $this->getMockBuilder('\Zend\EventManager\ListenerAggregateInterface')
                              ->getMockForAbstractClass();
        $aggregateMock->expects($this->exactly(2))->method('attach')->withConsecutive([ $this->events, 0], [$this->events, 1]);

        $this->setTargetListenerConfig(['TestAggregate', 'TestAggregate2' => 1]);
        $services = $this->getServiceManagerMock(['TestAggregate' => $aggregateMock, 'TestAggregate2' => $aggregateMock]);

        $this->target->createServiceWithName($services, 'no', 'no');
    }

    public function testAttachsListenerToEventManager()
    {
        $listenerMock = new \stdClass();
        $this->setTargetListenerConfig(['TestListener' => 'test-event', 'TestListener2' => ['test2-event', 'testMethod', 1]]);
        $services = $this->getServiceManagerMock(['TestListener' => $listenerMock, 'TestListener2' => $listenerMock]);
        $this->events->expects($this->exactly(2))->method('attach')
             ->withConsecutive([ ['test-event'], $listenerMock, 0 ], [ ['test2-event'], [$listenerMock, 'testMethod'], 1]);

        $this->target->createServiceWithName($services, '', '');
    }

    public function testNormalizesListenerOptions()
    {
        $listenerMock = new \stdClass();
        $singleEvent = 'event';
        $expectSingleEvent = [ $singleEvent ];
        $multiEvent  = [ 'event1', 'event2' ];
        $method = 'method';
        $additionalMethod = 'secondMethod';
        $priority = 1;
        $additionalPriority = 2;
        $lazy = true;
        $notLazy = false;

        $listeners = [
            'Test1' => $singleEvent,
            'Test2' => [ $singleEvent ],
            'Test3' => [ $multiEvent ],
            'Test4' => [ $singleEvent, $method ],
            'Test5' => [ $multiEvent, $method ],
            'Test6' => [ $singleEvent, $method, $priority ],
            'Test7' => [ $multiEvent, $method, $priority ],
            'Test8' => [ $singleEvent, $method, $priority, $lazy ],
            'Test9' => [ $multiEvent, $method, $priority, $lazy ],
            'Test10' => [ $singleEvent, $priority ],
            'Test11' => [ $multiEvent, $priority ],
            'Test12' => [ $singleEvent, $lazy ],
            'Test13' => [ $multiEvent, $lazy ],
            'Test14' => [ $singleEvent, $method, $additionalMethod ],
            'Test15' => [ $multiEvent, $lazy, $priority, $notLazy, $additionalPriority ],
        ];

        $expectedLazyListeners = [
            [ 'service' => 'Test8', 'event' => $expectSingleEvent, 'method' => $method, 'priority' => $priority ],
            [ 'service' => 'Test9', 'event' => $multiEvent, 'method' => $method, 'priority' => $priority ],
            [ 'service' => 'Test12', 'event' => $expectSingleEvent, 'method' => null, 'priority' => 0 ],
            [ 'service' => 'Test13', 'event' => $multiEvent, 'method' => null, 'priority' => 0],
        ];

        $servicesCfg = array_fill_keys([
            'Test1', 'Test2', 'Test3', 'Test4', 'Test5', 'Test6', 'Test7',
            'Test10', 'Test11', 'Test14', 'Test15'
        ], $listenerMock);

        $lazyAggregateMock = $this->getLazyAggregateMock();
        $lazyAggregateMock->expects($this->once())->method('setListeners')
            ->with($expectedLazyListeners)->willReturnSelf();

        $this->setTargetListenerConfig($listeners);
        $services = $this->getServiceManagerMock($servicesCfg, true);

        $this->events->expects($this->exactly(11))->method('attach')->withConsecutive(
            [ $expectSingleEvent, $listenerMock, 0 ],
            [ $expectSingleEvent, $listenerMock, 0 ],
            [ $multiEvent, $listenerMock, 0],
            [ $expectSingleEvent, [$listenerMock, $method], 0],
            [ $multiEvent, [$listenerMock,$method], 0],
            [ $expectSingleEvent, [$listenerMock,$method], $priority],
            [ $multiEvent, [$listenerMock,$method], $priority],
            [ $expectSingleEvent, $listenerMock, $priority ],
            [ $multiEvent, $listenerMock, $priority ],
            [ $expectSingleEvent, [$listenerMock,$additionalMethod], 0],
            [ $multiEvent, $listenerMock, $additionalPriority ]
        );

        $this->target->createServiceWithName($services, '', '');
    }

    public function testNormalizeListenerOptionsWithArrayHashSpecification()
    {
        $listenerMock = new \stdClass();
        $singleEvent = [ 'singleEvent' ];
        $priorityEvent = [ 'prioEvent' => 10 ];
        $methodEvent = [ 'methodEvent' => 'method' ];
        $singleAndPriorityAndMethodEvent = [ 'single', 'priority' => 10, 'method' => 'method'];
        $verboseEvent = [ 'verbose' => [ 'method' => 'method', 'priority' => 20]];
        $verboseFullEvent = [ 'verbose' => [ 'method' => ['method1', 'method2' => 25], 'priority' => 30]];

        $listeners = [
            'Test1' => [ 'events' => $singleEvent],
            'Test2' => [ 'events' => $priorityEvent],
            'Test3' => [ 'events' => $methodEvent],
            'Test4' => [ 'events' => $singleEvent, 'priority' => 12],
            'Test5' => [ 'events' => $priorityEvent, 'priority' => 12],
            'Test6' => [ 'events' => $methodEvent, 'priority' => 12],
            'Test7' => [ 'events' => $singleEvent, 'method' => 'method'],
            'Test8' => [ 'events' => $priorityEvent, 'method' => 'method'],
            'Test9' => [ 'events' => $methodEvent, 'method' => 'method2'],
            'Test10' => [ 'events' => $singleEvent, 'method' => 'method', 'priority' => 12],
            'Test11' => [ 'events' => $priorityEvent, 'method' => 'method2', 'priority' => 12],
            'Test12' => [ 'events' => $methodEvent, 'method' => 'method2', 'priority' => 12],
            'Test13' => [ 'events' => $singleAndPriorityAndMethodEvent, 'method'=> 'listenerMethod'],
            'Test14' => [ 'events' => $verboseEvent],
            'Test15' => [ 'events' => $verboseFullEvent],
            'Test16' => [ 'events' => $singleEvent, 'lazy' => true],
        ];

        $servicesCfg = array_fill_keys([
                                           'Test1', 'Test2', 'Test3', 'Test4', 'Test5', 'Test6', 'Test7',
                                           'Test8', 'Test9', 'Test10', 'Test11', 'Test12', 'Test13', 'Test14',
                                           'Test15'
                                       ], $listenerMock);
        $expectedLazyListeners = [
            [ 'service' => 'Test16', 'event' => $singleEvent, 'method' => null, 'priority' => 0 ],
        ];

        $lazyAggregateMock = $this->getLazyAggregateMock();
        $lazyAggregateMock->expects($this->once())->method('setListeners')
                          ->with($expectedLazyListeners)->willReturnSelf();

        $this->setTargetListenerConfig($listeners);
        $services = $this->getServiceManagerMock($servicesCfg, true);

        $this->events->expects($this->exactly(18))->method('attach')->withConsecutive(
                     [ $singleEvent, $listenerMock, 0 ],
                     [ [ 'prioEvent'], $listenerMock, 10 ],
                     [ [ 'methodEvent'], [$listenerMock,'method'], 0],
                     [ $singleEvent, $listenerMock, 12],
                     [ [ 'prioEvent' ], $listenerMock, 10],
                     [ [ 'methodEvent' ], [$listenerMock,'method'], 12],
                     [ $singleEvent, [$listenerMock, 'method'], 0],
                     [ [ 'prioEvent' ], [$listenerMock, 'method'], 10],
                     [ [ 'methodEvent' ], [$listenerMock, 'method'], 0],
                     [ $singleEvent, [$listenerMock,'method'], 12],
                     [ [ 'prioEvent' ], [$listenerMock,'method2'], 10],
                     [ [ 'methodEvent' ], [$listenerMock,'method'], 12],
                     [ [ 'single' ], [$listenerMock,'listenerMethod'], 0],
                     [ [ 'priority'], [$listenerMock,'listenerMethod'], 10],
                     [ [ 'method' ], [$listenerMock,'method'], 0],
                     [ [ 'verbose' ], [$listenerMock,'method'], 20],
                     [ [ 'verbose' ], [$listenerMock,'method1'], 30],
                     [ [ 'verbose' ], [$listenerMock,'method2'], 25]

        );

        $this->target->createServiceWithName($services, '', '');

    }
}

class AttachListenerTestListenerMock {}