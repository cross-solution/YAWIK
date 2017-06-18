<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener;

use Core\Listener\DeferredListenerAggregate;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Listener\DeferredListenerAggregate
 *
 * @covers \Core\Listener\DeferredListenerAggregate
 * @coversDefaultClass \Core\Listener\DeferredListenerAggregate
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class DeferredListenerAggregateTest extends \PHPUnit_Framework_TestCase
{

    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var array|null|DeferredListenerAggregate
     */
    protected $target = [
        'Core\Listener\DeferredListenerAggregate',
        'getTargetArgs',
        '@testFactoryMethodReturnsInstance' => false
    ];

    protected $inheritance = [ '\Zend\EventManager\ListenerAggregateInterface' ];
    
    protected $services;

    protected function getTargetArgs()
    {
        $this->services = $this->getMockBuilder(ServiceManager::class)
            ->setMethods(['has', 'get'])
            ->getMock();
        return [$this->services];
    }
    
    public function propertiesProvider()
    {
        return [
            [ 'listeners', [
                'value' => [ [] ],
                'setter_exception' => [ '\DomainException', 'Listener specification must be an array' ]
            ]],
            [ 'listeners', [
                'value' => [ [ 'event' => 'test' ] ],
                'setter_exception' => [ '\DomainException', 'Listener specification must be an array' ]
            ]],
            [ 'listeners', [
                'value' => [ [ 'service' => 'test' ] ],
                'setter_exception' => [ '\DomainException', 'Listener specification must be an array' ]
            ]],
            [ 'listeners', [
                'value' => [ [ 'event' => 'test', 'service' => 'service' ] ],
                'ignore_getter' => true,
                'post' => ['assertListenerSpecsProperty', [ '', '@target', '###' ] ],
            ]],
            [ 'listeners', [
                'value' => [ [ 'event' => 'test2', 'service' => 'someClass', 'method' => 'method', 'priority' => 12 ] ],
                'ignore_getter' => true,
                'post' => ['assertListenerSpecsProperty', [ '', '@target', '###' ] ],
            ]],
            [ 'listener', [
                'value' => 'test',
                'setter_args' => [ 'service', 'method', 10 ],
                'setter_value' => [ 'event' => 'test', 'service' => 'service', 'method' => 'method', 'priority' => 10, 'instance' => null ],
                'setter_assert' => 'assertListenerSpecsProperty',
                'ignore_getter' => true,
            ]],
            [ 'listener', [
                'value' => 'test',
                'setter_args' => [ 'service', 10 ],
                'setter_value' => [ 'event' => 'test', 'service' => 'service', 'method' => null, 'priority' => 10, 'instance' => null ],
                'setter_assert' => 'assertListenerSpecsProperty',
                'ignore_getter' => true,
            ]]
        ];
    }

    private function assertListenerSpecsProperty($name, $actual, $expected)
    {
        if (isset($expected['value'])) {
            $expected = array_merge(
                [
                    'event' => null,
                    'service' => null,
                    'method' => null,
                    'priority' => 0,
                    'instance' => null
                ],
                $expected['value'][0]
            );
        }

        $reflection = new \ReflectionClass($actual);
        $property = $reflection->getProperty('listenerSpecs');
        $property->setAccessible(true);
        $hooks = $property->getValue($actual);

        $hook = array_pop($hooks);

        $this->assertInstanceOf('Core\Listener\DeferredListenerAggregate', $actual, 'Fluent interface broken.');
        $this->assertEquals($hook, $expected);
    }

    /**
     * @covers ::attach
     * @covers ::detach
     */
    public function testAttachesAndDetachesToAndFromAnEventManager()
    {
    	$testListener = new DLATListenerMock();
        $this->target->setListener('test01', 'service01');
        $this->target->setListener('test02', 'service02', 10);
		
        //In ZF3 EventManager detach should be a callable or it will throws an error
        $callback = [$testListener,'callback'];
        $events = $this->getMockBuilder(EventManager::class)
            ->setMethods(['attach', 'detach'])
            ->getMock();
        $events->expects($this->exactly(2))
			->method('attach')
            ->withConsecutive(
            	[ $this->equalTo('test01'), $this->anything(), $this->equalTo(0) ],
				[ $this->equalTo('test02'), $this->anything(), $this->equalTo(10) ]
            )
            ->will($this->onConsecutiveCalls(
            	[$testListener,'callback'], [$testListener,'callback'])
            );

        $events
	        ->expects($this->exactly(2))
	        ->method('detach')
            ->withConsecutive(
            	[$callback], [$callback] )
            ->will($this->onConsecutiveCalls([true, false]))
        ;

        $this->target->attach($events);
        $this->assertFalse($this->target->detach($events));
    }

    /**
     * @covers ::__call
     */
    public function testCallThrowsExceptionIfMethodDoesNotStartWithDo()
    {
        $this->setExpectedException('\BadMethodCallException', 'Unknown method "unknownMethod"');
        $this->target->unknownMethod();
    }

    /**
     * @covers ::__call
     */
    public function testCallThrowsExceptionIfNoSpecificationIsFound()
    {
        $this->setExpectedException('\UnexpectedValueException', 'No deferred listener spec');
        $this->target->doInexistantListener();
    }

    public function listenerProvider()
    {
        return [
            [ 'nonExistant', null ],
            [ '\CoreTest\Listener\DLATListenerMock', null ],
            [ 'listener', new DLATListenerMock() ],
            [ 'listener', new DLATListenerMock(), 'invoke' ],
            [ 'listener', new DLATListenerMock(), 'method', 'callback' ],
            [ 'listener', new DLATListenerMock(), 'methodButNone', 'noMethod' ],
            [ 'listener', new DLATNonInvokableListenerMock() ],
        ];
    }

    /**
     * @dataProvider listenerProvider
     *
     * @covers ::__call
     *
     * @param $service
     * @param $listener
     * @param $check
     * @param $method
     */
    public function testListenerCreationAndInvokation($service, $listener, $check = null, $method = null)
    {
        if (null === $listener) {
            if (!class_exists($service, true)) {
                $this->setExpectedException('\UnexpectedValueException', 'Cannot create deferred listener "' . $service );
            }
            $this->services->expects($this->once())->method('has')->with($service)->willReturn(false);
            $this->services->expects($this->never())->method('get');
        } else {
            $this->services->expects($this->once())->method('has')->with($service)->willReturn(true);
            $this->services->expects($this->once())->method('get')->with($service)->willReturn($listener);
        }

        if ($listener instanceOf DLATNonInvokableListenerMock) {
            $this->setExpectedException('\UnexpectedValueException', 'Deferred listener');
        }

        $events = new EventManager();

        $this->target->setListener('test', $service, $method);
        $this->target->attach($events);

        $events->trigger('test');

        switch ($check) {
            case 'methodButNone':
                $this->assertFalse($listener->callbackCalled, 'callback method was called, but should\'ve been not!');
                break;

            case 'method':
                $this->assertTrue($listener->callbackCalled);
                $this->assertInstanceOf('\Zend\EventManager\EventInterface', $listener->callbackEvent);
                break;

            case 'invoke':
                $this->assertTrue($listener->invoked);
                break;
        }

    }

    public function testFactoryMethodReturnsInstance()
    {
        $services = new ServiceManager();
        $instance = DeferredListenerAggregate::factory($services);

        $this->assertInstanceOf(DeferredListenerAggregate::class, $instance);
        $this->assertAttributeSame($services, 'serviceManager', $instance);
    }
}

class DLATListenerMock
{
    public $invoked = false;
    public $callbackCalled = false;
    public $callbackEvent;

    public function __invoke()
    {
        $this->invoked = true;
    }

    public function callback($e)
    {
        $this->callbackCalled = true;
        $this->callbackEvent = $e;
    }
}

class DLATNonInvokableListenerMock
{

}