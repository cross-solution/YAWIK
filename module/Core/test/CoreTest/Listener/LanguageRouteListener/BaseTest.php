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

use Core\Options\ModuleOptions;
use Zend\EventManager\EventManager;
use Core\Listener\LanguageRouteListener;
use Core\I18n\Locale as LocaleService;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

/**
 * Base tests for \Core\Listener\LanguageRouteListener
 *
 * @covers \Core\Listener\LanguageRouteListener
 * @coversDefaultClass \Core\Listener\LanguageRouteListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @group Core
 * @group Core.Listener
 * @group Core.Listener.LanguageRouteListener
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target;

    private $inheritance = [ ListenerAggregateInterface::class ];

    public function setUp()
    {
        $this->target = new LanguageRouteListener(new LocaleService(['xx' => 'xx_XX']),new ModuleOptions());
    }
    
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
    	$callableListener = [new CallableListenerMock(),'doSomething'];
    	
        $events = $this
            ->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['attach', 'detach'])
            ->getMock();

        $events
	        ->expects($this->any())
	        ->method('attach')
	        ->will(
	        	$this->onConsecutiveCalls($callableListener, $callableListener)
	        )
        ;

        $events
            ->expects($this->exactly(2))
            ->method('detach')
            ->withConsecutive([$callableListener], [$callableListener])
            ->will($this->onConsecutiveCalls(true, false))
        ;

        $this->target->attach($events);
        $this->target->detach($events);

        $this->assertAttributeEquals([1 => $callableListener], 'listeners', $this->target);
    }
}

class CallableListenerMock
{
	public function doSomething(){}
}
