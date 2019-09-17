<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener;

use PHPUnit\Framework\TestCase;

use Core\EventManager\ListenerAggregateTrait;
use Core\Listener\AjaxRouteListener;
use Core\Listener\Events\AjaxEvent;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Core\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;

/**
 * Tests for \Core\Listener\AjaxRouteListener
 *
 * @covers \Core\Listener\AjaxRouteListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Listener
 */
class AjaxRouteListenerTest extends TestCase
{
    use SetupTargetTrait, TestUsesTraitsTrait, TestDefaultAttributesTrait;

    private $target = [
        AjaxRouteListener::class,
        'getTargetArgs',
        '@testConstruction' => false,
        '@testInheritance' => '@testUsesTraits',
        '@testDefaultAttributes' => '@testUsesTraits',
        '@testUsesTraits' => [ 'as_reflection' => true ],
    ];

    private $traits = [ ListenerAggregateTrait::class ];

    private $attributes = [
        'events' => [ [ MvcEvent::EVENT_ROUTE, 'onRoute', 100 ] ],
    ];

    private function getTargetArgs()
    {
        $this->ajaxEventManagerMock = $this->getMockBuilder(EventManager::class)->disableOriginalConstructor()
            ->setMethods(['getEvent', 'triggerEventUntil'])->getMock();

        return [ $this->ajaxEventManagerMock ];
    }

    public function testConstruction()
    {
        $events = new EventManager();
        $target = new AjaxRouteListener($events);

        $this->assertAttributeSame($events, 'ajaxEventManager', $target);
    }

    public function testOnRouteReturnsEarlyIfNotAjaxRequestOrAjaxParamIsNotSet()
    {
        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResponse'])
            ->getMock();
        $event->expects($this->never())->method('getResponse');

        $request = new Request();

        $event->setRequest(new Request());

        $this->target->onRoute($event);
        $query = clone $request->getQuery();
        $request->getQuery()->set('ajax', 'test');

        $this->target->onRoute($event);

        $request->setQuery($query);
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');

        $this->target->onRoute($event);
    }

    public function testOnRouteThrowsExceptionIfNoResult()
    {
        $event = new AjaxEvent();
        $this->ajaxEventManagerMock->expects($this->once())->method('getEvent')->with('test', $this->target)->willReturn($event);
        $this->ajaxEventManagerMock->expects($this->once())->method('triggerEventUntil')
            ->with($this->isInstanceOf('\Closure'), $event)
            ->willReturn(new ResponseCollection());

        $this->expectException('\UnexpectedValueException');

        $this->target->onRoute($this->getMvcEvent());
    }

    public function testOnRouteReturnsJsonString()
    {
        $event = $this->getMvcEvent();
        $result1 = ['test' => 'success'];
        $results = new ResponseCollection();
        $results->push($result1);
        $this->ajaxEventManagerMock->expects($this->once())->method('getEvent')->with('test', $this->target)->willReturn(new AjaxEvent());
        $this->ajaxEventManagerMock->expects($this->once())->method('triggerEventUntil')
                                   ->with($this->isInstanceOf('\Closure'), $this->isInstanceOf(AjaxEvent::class))
                                   ->willReturn($results);

        $response = $this->target->onRoute($event);

        $this->assertEquals('{"test":"success"}', $response->getContent());
    }

    private function getMvcEvent()
    {
        $request = new Request();
        $request->getHeaders()->addHeaderline('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->getQuery()->set('ajax', 'test');
        $response = new Response();
        $event = new MvcEvent();

        $event->setRequest($request)->setResponse($response);

        return $event;
    }
}
