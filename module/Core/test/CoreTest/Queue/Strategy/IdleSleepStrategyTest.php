<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue\Strategy;

use PHPUnit\Framework\TestCase;

use Core\Queue\MongoQueue;
use Core\Queue\Strategy\IdleSleepStrategy;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SlmQueue\Queue\AbstractQueue;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\ProcessIdleEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * Tests for \Core\Queue\Strategy\IdleSleepStrategy
 *
 * @covers \Core\Queue\Strategy\IdleSleepStrategy
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class IdleSleepStrategyTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = IdleSleepStrategy::class;

    private $inheritance = [ AbstractStrategy::class ];


    public function testSetDuration()
    {
        $this->target->setDuration(10);

        static::assertEquals(10, $this->target->duration);
    }

    public function testAttach()
    {
        $events = $this->prophesize(EventManagerInterface::class);
        $events->attach(AbstractWorkerEvent::EVENT_PROCESS_IDLE, [$this->target, 'onIdle'], 1)
               ->willReturn('handle')
               ->shouldBeCalled();

        $this->target->attach($events->reveal());

        static::assertAttributeEquals(['handle'], 'listeners', $this->target);
    }

    public function testOnIdleWithMongoQueue()
    {
        $queue = $this->createMock(MongoQueue::class);
        $event = $this->createConfiguredMock(ProcessIdleEvent::class, ['getQueue' => $queue]);

        $start = microtime(true);
        $this->target->onIdle($event);
        $time = microtime(true) - $start;

        static::assertGreaterThan(1, $time);
    }

    public function testOnIdleWithOtherQueue()
    {
        $queue = $this->createMock(AbstractQueue::class);
        $event = $this->createConfiguredMock(ProcessIdleEvent::class, ['getQueue' => $queue]);

        $start = microtime(true);
        $this->target->onIdle($event);
        $time = microtime(true) - $start;

        static::assertLessThan(1, $time);
    }
}
