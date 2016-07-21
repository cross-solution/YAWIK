<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener\LanguageRouteListener;

use Zend\EventManager\EventManager;
use Core\Listener\LanguageRouteListener;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

/**
 * Base tests for \Core\Listener\LanguageRouteListener
 * 
 * @covers \Core\Listener\LanguageRouteListener
 * @coversDefaultClass \Core\Listener\LanguageRouteListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Listener
 * @group Core.Listener.LanguageRouteListener
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = LanguageRouteListener::class;

    private $inheritance = [ ListenerAggregateInterface::class ];

    private $properties = [
        [ 'defaultLanguage', ['value' => 'en', 'ignore_setter' => true]],
        [ 'supportedLanguages', ['value' => ['xx' => 'xx_XX'], 'expect_property' => [['xx' => 'xx_XX']], 'setter_value' => null]],
    ];

    public function testAttachsToExpectedEvents()
    {
        $events = $this
            ->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['attach'])
            ->getMock();

        $events
            ->expects($this->exactly(2))
            ->method('attach')
            ->withConsecutive(
                [MvcEvent::EVENT_ROUTE, [$this->target, 'onRoute'], 1],
                [MvcEvent::EVENT_DISPATCH_ERROR, [$this->target, 'onDispatchError'], 1]
            )
            ->will($this->onConsecutiveCalls('listener1', 'listener2'));

        $this->target->attach($events);

        $expect = ['listener1', 'listener2'];

        $this->assertAttributeEquals($expect, 'listeners', $this->target);
    }

    /**
     * @covers ::detach()
     */
    public function testDetach()
    {
        $events = $this
            ->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['attach', 'detach'])
            ->getMock();

        $events->expects($this->any())->method('attach')->will($this->onConsecutiveCalls('listener1', 'listener2'));

        $events
            ->expects($this->exactly(2))
            ->method('detach')
            ->withConsecutive(['listener1'], ['listener2'])
            ->will($this->onConsecutiveCalls(true, false))
        ;

        $this->target->attach($events);
        $this->target->detach($events);

        $this->assertAttributeEquals([1 => 'listener2'], 'listeners', $this->target);
    }
}


