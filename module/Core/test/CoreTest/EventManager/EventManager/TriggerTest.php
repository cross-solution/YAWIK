<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\EventManager\EventManager;

use PHPUnit\Framework\TestCase;

use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;

/**
 * Tests for \Core\EventManager\EventManager
 *
 * @covers \Core\EventManager\EventManager
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.EventManager
 */
class TriggerTest extends TestCase
{
    /**
     * @var TriggerTestEventManagerMock
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new TriggerTestEventManagerMock();
    }

    public function testNewEventIsCreatedIfNoEventInstanceIsProvided()
    {
        $this->target->trigger('test', null, []);
        $this->assertTrue($this->target->getEventCalled, 'No Event was created!');

        $this->target->getEventCalled = false;
        $this->target->triggerUntil(function () {
        }, 'test', null, []);
        $this->assertTrue($this->target->getEventCalled, 'No Event was created (triggerUntil)');
    }

    public function testNoEventIsCreatedIfEventInstanceIsProvided()
    {
        $event = new Event();
        $this->target->trigger($event);
        $this->target->triggerUntil(function () {
        }, $event);

        $this->assertFalse($this->target->getEventCalled, 'An event was created!');
    }

    public function testCallbackIsPassedAlong()
    {
        $event = new Event();
        $callback = function () {
        };
        $this->target->triggerUntil($callback, $event);

        $this->assertSame($callback, $this->target->callback);
    }
}

class TriggerTestEventManagerMock extends \Core\EventManager\EventManager
{
    public $getEventCalled = false;
    public $callback = false;

    public function getEvent($name = null, $target = null, $params = null)
    {
        $this->getEventCalled = true;
        return parent::getEvent($name, $target, $params);
    }

    protected function triggerListeners(EventInterface $e, callable $callback = null)
    {
        $this->callback = $callback;
    }
}
