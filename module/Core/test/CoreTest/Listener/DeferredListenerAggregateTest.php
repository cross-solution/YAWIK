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
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use CoreTestUtils\TestCase\SetterGetterTrait;
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

    use AssertInheritanceTrait, SetterGetterTrait;

    protected $target = 'Core\Listener\DeferredListenerAggregate';

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
        $target = $this->getMockBuilder(DeferredListenerAggregate::class)
            ->setMethods(['setListener'])
            ->disableOriginalConstructor()
            ->getMock();
        $target->expects($this->any())->method('setListener')
            ->withConsecutive(
                ['test', 'service', null, 0],
                ['test2', 'someClass', 'method', 12]
            );

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
                'target' => $target,
                'ignore_getter' => true,
                'property_assert' => 'verifyAddListenersTestTarget',
            ]],
            [ 'listeners', [
                'value' => [ [ 'event' => 'test2', 'service' => 'someClass', 'method' => 'method', 'priority' => 12 ] ],
                'target' => $target,
                'expect_property' => $target,
                'property_assert' => 'verifyAddListenersTestTarget',
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

    private function verifyAddListenersTestTarget($name, $mock)
    {
        if ($mock->__phpunit_hasMatchers()) {
            $this->addToAssertionCount(1);
        }
        $mock->__phpunit_verify();
    }

    private function assertListenerSpecsProperty($name, $actual, $expected)
    {
        $reflection = new \ReflectionClass($actual);
        $property = $reflection->getProperty('listenerSpecs');
        $property->setAccessible(true);
        $hooks = $property->getValue($actual);

        $hook = array_pop($hooks);

        $this->assertEquals($hook, $expected);
    }

    /**
     * @covers ::attach
     * @covers ::detach
     */
    public function testAttachesAndDetachesToAndFromAnEventManager()
    {
        $this->target->setHook('test', 'service');
        $this->target->setHook('test2', 'service2', 10);

        $events = $this->getMockBuilder(EventManager::class)
            ->setMethods(['attach', 'detach'])
            ->getMock();
        $events->expects($this->exactly(2))->method('attach')
            ->withConsecutive(    [ $this->equalTo('test'), $this->anything(), $this->equalTo(0) ],
                                  [ $this->equalTo('test2'), $this->anything(), $this->equalTo(10) ]
                              )
            ->will($this->onConsecutiveCalls(0, 1));

        $events->expects($this->exactly(2))->method('detach')
            ->withConsecutive([ 0], [1] )->will($this->onConsecutiveCalls([true, false]));

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