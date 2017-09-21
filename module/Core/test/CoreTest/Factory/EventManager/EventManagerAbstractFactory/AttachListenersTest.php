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
use Core\Listener\DeferredListenerAggregate;
use Zend\ServiceManager\ServiceManager;


/**
 * Tests for \Core\Factory\EventManager\EventManagerAbstractFactory::attachListeners
 * and       \Core\Factory\EventManager\EventManagerAbstractFactory::normalizeListenerOptions
 *
 * @covers \Core\Factory\EventManager\EventManagerAbstractFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
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
        $defaultListenerMock = new AttachListenerTestListenerMock();

        $lazyAggregateMock = $this->getLazyAggregateMock($expectLazyListeners);
        if ($expectLazyListeners) {
            $lazyAggregateMock->expects($this->once())->method('attach');
            $getMap[] = [ 'Core/Listener/DeferredListenerAggregate', $lazyAggregateMock ];
        }

        foreach ($listeners as $serviceName => $listenerMock) {
            if (is_int($serviceName)) {
                $serviceName = $listenerMock;
                $listenerMock = $defaultListenerMock;
            }

            if (false === $listenerMock) {
                $hasMap[] = [ $serviceName, false];
            } else {
                $hasMap[] = [ $serviceName, true ];
                $getMap[] = [ $serviceName, $listenerMock ];
            }
        }

        $services->expects($this->atLeast(count($hasMap)))
	        ->method('has')
	        ->will($this->returnValueMap($hasMap))
        ;

        $services->expects($this->exactly(count($getMap)))
                 ->method('get')
                 ->will($this->returnValueMap($getMap))
        ;

        return $services;
    }

    protected function getLazyAggregateMock($configureSetHooks = null)
    {
        if (!$this->lazyAggregateMock) {
            $this->lazyAggregateMock = $this->getMockBuilder('\Core\Listener\DeferredListenerAggregate')
                ->disableOriginalConstructor()
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

    //public function testLazyListenersAreAttachedToDeferredListenerAggregate()
    //{
        //$this->setTargetListenerConfig(['TestListener' => ['test-event', true]]);
        //$services = $this->getServiceManagerMock([], true);
        //$this->target->createServiceWithName($services, 'irrlelevant', 'irrelevant');
    //}

    public function testPullsListenersFromServiceManager()
    {
        $this->setTargetListenerConfig(['TestListener' => ['test-event','doSomething',1]]);
        $services = $this->getServiceManagerMock(['TestListener']);
        $this->target->createServiceWithName($services, 'irrelevant', 'irrelevant');
    }

    public function testCreatesListenersInstances()
    {
        $this->setTargetListenerConfig(
        	['\CoreTest\Factory\EventManager\EventManagerAbstractFactory\AttachListenerTestListenerMock' => ['test-event','doSomething',1]]
        );
        $services = $this->getServiceManagerMock([
        	'\CoreTest\Factory\EventManager\EventManagerAbstractFactory\AttachListenerTestListenerMock' => false
        ]);

        $this->target->createServiceWithName($services, 'irrelevant', 'irrelevant');
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
		$aggregateMock->expects($this->exactly(2))->method('attach')->withConsecutive([ $this->events, 1], [$this->events, -100]);
		
		$this->setTargetListenerConfig(['TestAggregate', 'TestAggregate2' => -100]);
		$services = $this->getServiceManagerMock(['TestAggregate' => $aggregateMock, 'TestAggregate2' => $aggregateMock]);
		
		$this->target->createServiceWithName($services, 'no', 'no');
	}

    public function testAttachsListenerToEventManager()
    {
        $listenerMock = new AttachListenerTestListenerMock();
        $this->setTargetListenerConfig([
        	'TestListener1' => ['test-event1','doSomething',1],
	        'TestListener2' => ['test-event2','doSomething',2]
        ]);
        $services = $this->getServiceManagerMock([
        	'TestListener1' => $listenerMock,
	        'TestListener2' => $listenerMock
        ]);
        $this->events
	        ->expects($this->exactly(2))
	        ->method('attach')
	        ->withConsecutive(
	        	['test-event1', [$listenerMock,'doSomething'], 1 ],
		        ['test-event2', [$listenerMock, 'doSomething'], 2 ]
	        )
        ;

        $this->target->createServiceWithName($services, '', '');
    }

    public function testNormalizesListenerOptions()
    {
        $listenerMock = new AttachListenerTestListenerMock();
        $singleEvent = 'event';
        $expectSingleEvent = $singleEvent;
        $multiEvent  = [ 'event1', 'event2' ];
        $method = 'doSomething';
        $additionalMethod = 'doSomethingElse';
        $priority = 1;
        $additionalPriority = 2;
        $lazy = true;
        $notLazy = false;

        $listeners = [
            'Test01' => [ $singleEvent, $method],
            'Test02' => [ $singleEvent, $method ],
            'Test03' => [ $multiEvent, $method ],
            'Test04' => [ $singleEvent, $method ],
            'Test05' => [ $multiEvent, $method ],
            'Test06' => [ $singleEvent, $method, $priority ],
            'Test07' => [ $multiEvent, $method, $priority ],
            'Test08' => [ $singleEvent, $method, $priority, $lazy ],
            'Test09' => [ $multiEvent, $method, $priority, $lazy ],
            'Test10' => [ $singleEvent,$method, $priority ],
            'Test11' => [ $multiEvent, $method, $priority ],
            'Test12' => [ $singleEvent,$method, $lazy ],
            'Test13' => [ $multiEvent,$method, $lazy ],
            'Test14' => [ $singleEvent, $method, $additionalMethod ],
            'Test15' => [ $multiEvent, $method, $lazy, $priority, $notLazy, $additionalPriority ],
        ];

        $expectedLazyListeners = [
            [ 'service' => 'Test08', 'event' => $expectSingleEvent, 'method' => $method, 'priority' => $priority ],
            [ 'service' => 'Test09', 'event' => 'event1', 'method' => $method, 'priority' => $priority ],
            [ 'service' => 'Test09', 'event' => 'event2', 'method' => $method, 'priority' => $priority ],
            [ 'service' => 'Test12', 'event' => $expectSingleEvent, 'method' => $method, 'priority' => 1 ],
            [ 'service' => 'Test13', 'event' => 'event1', 'method' => $method, 'priority' => 1],
            [ 'service' => 'Test13', 'event' => 'event2', 'method' => $method, 'priority' => 1],
        ];

        $servicesCfg = array_fill_keys([
            'Test01', 'Test02', 'Test03', 'Test04', 'Test05', 'Test06', 'Test07',
            'Test10', 'Test11', 'Test14', 'Test15'
        ], $listenerMock);

        $lazyAggregateMock = $this->getLazyAggregateMock();
        $lazyAggregateMock->expects($this->once())
			->method('setListeners')
            ->with($expectedLazyListeners)
            ->willReturnSelf()
        ;

        $this->setTargetListenerConfig($listeners);
        $services = $this->getServiceManagerMock($servicesCfg, true);
		$expectedCallback = [$listenerMock,'doSomething'];
        $this->events->expects($this->exactly(16))
			->method('attach')
			->withConsecutive(
            [ $expectSingleEvent, $expectedCallback, 1 ],
            [ $expectSingleEvent, $expectedCallback, 1 ],
            [ 'event1', $expectedCallback, 1],
            [ 'event2', $expectedCallback, 1],
            [ $expectSingleEvent, $expectedCallback, 1],
            [ 'event1', $expectedCallback, 1],
            [ 'event2', $expectedCallback, 1],
            [ $expectSingleEvent, $expectedCallback, $priority],
            [ 'event1', $expectedCallback, $priority],
	        [ 'event2', $expectedCallback, $priority],
            [ $expectSingleEvent, $expectedCallback, $priority ],
	        [ 'event1', $expectedCallback, $priority ],
            [ 'event2', $expectedCallback, $priority ],
            [ $expectSingleEvent, [$listenerMock,$additionalMethod], 1],
            [ 'event1', $expectedCallback, $additionalPriority ],
            [ 'event2', $expectedCallback, $additionalPriority ]
        );

        $this->target->createServiceWithName($services, '', '');
    }

    public function testNormalizeListenerOptionsWithArrayHashSpecification()
    {
        $listenerMock = new AttachListenerTestListenerMock();
        $singleEvent = [ 'singleEvent' ];
        $priorityEvent = [ 'prioEvent' => 10 ];
        $methodEvent = [ 'methodEvent' => 'doSomething' ];
        $singleAndPriorityAndMethodEvent = [ 'single', 'priority' => 10, 'method' => 'doSomething'];
        $verboseEvent = [ 'verbose' => [ 'method' => 'doSomething', 'priority' => 20]];
        $verboseFullEvent = [ 'verbose' => [ 'method' => ['doSomething', 'doSomethingElse' => 25], 'priority' => 30]];
        $multiEvent = [ 'multi1', 'multi2' ];

        $listeners = [
            'Test01' => [ 'events' => $singleEvent, 'method' => 'doSomething'],
            'Test02' => [ 'events' => $priorityEvent, 'method' => 'doSomething'],
            'Test03' => [ 'events' => $methodEvent, 'method' => 'doSomething'],
            'Test04' => [ 'events' => $singleEvent, 'method'=>'doSomething', 'priority' => 12],
            'Test05' => [ 'events' => $priorityEvent, 'method'=>'doSomething', 'priority' => 12],
            'Test06' => [ 'events' => $methodEvent,'method'=>'doSomething', 'priority' => 12],
            'Test07' => [ 'events' => $singleEvent, 'method' => 'doSomething'],
            'Test08' => [ 'events' => $priorityEvent, 'method' => 'doSomething'],
            'Test09' => [ 'events' => $methodEvent, 'method' => 'doSomethingElse'],
            'Test10' => [ 'events' => $singleEvent, 'method' => 'doSomething', 'priority' => 12],
            'Test11' => [ 'events' => $priorityEvent, 'method' => 'doSomethingElse', 'priority' => 12],
            'Test12' => [ 'events' => $methodEvent, 'method' => 'doSomethingElse', 'priority' => 12],
            'Test13' => [ 'events' => $singleAndPriorityAndMethodEvent, 'method'=> 'doSomething'],
            'Test14' => [ 'events' => $verboseEvent],
            'Test15' => [ 'events' => $verboseFullEvent],
            'Test16' => [ 'events' => $singleEvent, 'lazy' => true],
            'Test17' => [ 'events' => $multiEvent, 'method' => 'multiMethod' ],
        ];
	
	    $servicesCfg = array_fill_keys([
		    'Test01', 'Test02', 'Test03', 'Test04', 'Test05', 'Test06', 'Test07',
		    'Test08', 'Test09', 'Test10', 'Test11', 'Test12', 'Test13', 'Test14',
		    'Test15', 'Test17',
	    ], $listenerMock);
        $expectedLazyListeners = [
            [ 'service' => 'Test16', 'event' => $singleEvent[0], 'method' => null, 'priority' => 1 ],
        ];

        $lazyAggregateMock = $this->getLazyAggregateMock();
        $lazyAggregateMock
	        ->expects($this->once())
	        ->method('setListeners')
	        ->with($expectedLazyListeners)
	        ->willReturnSelf()
        ;

        $this->setTargetListenerConfig($listeners);
        $services = $this->getServiceManagerMock($servicesCfg, true);
		
        $callback1 = [$listenerMock,'doSomething'];
	    $callback2 = [$listenerMock,'doSomethingElse'];
        $this->events
	        ->expects($this->exactly(20))
	        ->method('attach')
	        ->withConsecutive(
		        [ $singleEvent[0], $callback1, 1 ],
		        [ 'prioEvent', $callback1, 10 ],
		        [ 'methodEvent', $callback1, 1],
		        [ $singleEvent[0], $callback1, 12],
		        [ 'prioEvent', $callback1, 10],
		        [ 'methodEvent', $callback1, 12],
		        [ $singleEvent[0], $callback1, 1],
		        [ 'prioEvent' , $callback1, 10],
		        [ 'methodEvent', $callback1, 1],
		        [ $singleEvent[0], $callback1, 12],
		        [ 'prioEvent', $callback2, 10],
		        [ 'methodEvent', $callback1, 12],
		        [ 'single', $callback1, 1 ],
                [ 'method' , $callback1, 1],
		        [ 'priority', $callback1, 10],
		        //[ [ 'method' ], $callback1, 1],
		        [ 'verbose', $callback1, 20],
		        [ 'verbose', $callback1, 30],
		        [ 'verbose', $callback2, 25],
                [ 'multi1', [$listenerMock,'multiMethod'], 1],
                [ 'multi2', [$listenerMock,'multiMethod'], 1]
            )
        ;

        $this->target->createServiceWithName($services, '', '');

    }
}

class AttachListenerTestListenerMock {
	public function doSomething(){}
	public function doSomethingElse(){}
	public function multiMethod(){}
}
