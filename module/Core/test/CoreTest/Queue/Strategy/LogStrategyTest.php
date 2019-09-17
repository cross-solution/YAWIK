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
use Core\Queue\Strategy\LogStrategy;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Prophecy\Argument;
use SlmQueue\Job\AbstractJob;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\FinishEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * Tests for \Core\Queue\Strategy\LogStrategy
 *
 * @covers \Core\Queue\Strategy\LogStrategy
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class LogStrategyTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|LogStrategy
     */
    private $target = [
        LogStrategy::class,
        '@testConstructor*' => false,
    ];

    private $inheritance = [ AbstractStrategy::class ];

    private $attributes = [
        'tmpl' => [
            'queue' => '%s queue: %s',
            'job'   => '{ %s } [ %s ] %s',
        ],
        'injectLogger' => true
    ];

    public function testConstructorWithoutOptions()
    {
        $target = new LogStrategy();

        static::assertAttributeEmpty('logger', $target);
    }

    public function testConstructorWithLoggerInterface()
    {
        $logger = new Logger();
        $target = new LogStrategy($logger);

        static::assertAttributeSame($logger, 'logger', $target);
    }

    public function testConstructorWithOptions()
    {
        $expectTmpl = [
            'queue' => 'queue template',
            'job'   => 'job template',
        ];

        $options = [
            'logger' => new Logger(),
            'logQueueEventsTemplate' => $expectTmpl['queue'],
            'logJobEventsTemplate' => $expectTmpl['job'],
        ];

        $target = new LogStrategy($options);

        static::assertAttributeSame($options['logger'], 'logger', $target);
        static::assertAttributeEquals($expectTmpl, 'tmpl', $target);
    }

    public function testConstructorThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Options must be of type');

        new LogStrategy('a string');
    }

    public function testSetLogger()
    {
        $logger = new Logger();
        $this->target->setLogger($logger);

        static::assertSame($logger, $this->target->getLogger());
    }

    public function testGetLoggerReturnsNullLoggerIfNoneIsSet()
    {
        $logger = $this->target->getLogger();

        static::assertTrue((new \ReflectionClass($logger))->isAnonymous(), 'getLogger did not return an anonymous NullLogger.');
        static::assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function testSetLogTemplates()
    {
        $expectTmpl = [
            'queue' => 'Custom queue template',
            'job'   => 'Custom job template',
        ];

        $this->target->setLogQueueEventsTemplate($expectTmpl['queue']);
        $this->target->setLogJobEventsTemplate($expectTmpl['job']);

        static::assertAttributeEquals($expectTmpl, 'tmpl', $this->target);
    }

    public function testSetAndGetInjectLoggerFlag()
    {
        static::assertTrue($this->target->injectLogger());
        $this->target->injectLogger(false);
        static::assertFalse($this->target->injectLogger());
    }

    public function testAttachsToEvents()
    {
        $events = $this->prophesize(EventManagerInterface::class);
        $events
            ->attach(AbstractWorkerEvent::EVENT_BOOTSTRAP, [$this->target, 'logBootstrap'], 1000)
            ->willReturn('handle')
            ->shouldBeCalled()
        ;
        $events
            ->attach(AbstractWorkerEvent::EVENT_FINISH, [$this->target, 'logFinish'], 1000)
            ->willReturn('handle')
            ->shouldBeCalled()
        ;
        $events
            ->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this->target, 'logJobStart'], 1000)
            ->willReturn('handle')
            ->shouldBeCalled()
        ;
        $events
            ->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this->target, 'logJobEnd'], -1000)
            ->willReturn('handle')
            ->shouldBeCalled()
        ;
        $events
            ->attach(AbstractWorkerEvent::EVENT_PROCESS_IDLE, [$this->target, 'injectLoggerInEvent'], 1000)
            ->willReturn('handle')
            ->shouldBeCalled()
        ;
        $events
            ->attach(AbstractWorkerEvent::EVENT_PROCESS_STATE, [$this->target, 'injectLoggerInEvent'], 1000)
            ->wilLReturn('handle')
            ->shouldBeCalled()
        ;
        /* @var EventManagerInterface $eventsMock */
        $eventsMock = $events->reveal();

        $this->target->attach($eventsMock);
        static::assertAttributeEquals(['handle', 'handle', 'handle', 'handle', 'handle', 'handle'], 'listeners', $this->target);
    }

    public function testLogsQueueEvents()
    {
        $queue = $this->prophesize(MongoQueue::class);
        $queue->getName()->willReturn('queue');
        $queueMock = $queue->reveal();

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->info('Start queue: queue')->shouldBeCalled();
        $logger->info('Stop queue: queue')->shouldBeCalled();
        /* @var LoggerInterface $loggerMock */
        $loggerMock = $logger->reveal();

        $workerMock = new \stdClass;
        $bootstrapEvent = $this->prophesize(BootstrapEvent::class);
        $bootstrapEvent->getQueue()->willReturn($queueMock);
        $bootstrapEvent->getWorker()->willReturn($workerMock);
        $bootstrapEvent->setParam('logger', $loggerMock)->willReturn(null);
        /* @var BootstrapEvent $bootstrapEventMock */
        $bootstrapEventMock = $bootstrapEvent->reveal();

        $finishEvent = $this->prophesize(FinishEvent::class);
        $finishEvent->getQueue()->wilLReturn($queueMock);
        $finishEvent->setParam('logger', $loggerMock)->willReturn(null);
        /* @var FinishEvent $finishEventMock */
        $finishEventMock = $finishEvent->reveal();


        $this->target->setLogger($loggerMock);
        $this->target->logBootstrap($bootstrapEventMock);
        $this->target->logFinish($finishEventMock);
    }

    public function testLogsJobStartEvent()
    {
        $queue = $this->prophesize(MongoQueue::class);
        $queue->getName()->willReturn('queue');
        $queueMock = $queue->reveal();

        $job = new class extends AbstractJob {
            public function getId()
            {
                return 'jobId';
            }
            public function execute()
            {
            }
        };

        $logger = $this->prophesize(LoggerInterface::class);
        $logger
            ->info(Argument::allOf(
                Argument::containingString('START'),
                Argument::containingString($job->getId())
            ))
            ->shouldBeCalled()
        ;
        /* @var LoggerInterface $loggerMock */
        $loggerMock = $logger->reveal();

        $event = $this->prophesize(ProcessJobEvent::class);
        $event->getQueue()->willReturn($queueMock);
        $event->getJob()->willReturn($job);
        $event->setParam('logger', $loggerMock)->willReturn(null);
        /* @var ProcessJobEvent $eventMock */
        $eventMock = $event->reveal();

        $this->target->setLogger($loggerMock);
        $this->target->logJobStart($eventMock);
    }

    public function testLogJobStartInjectsLoggerIntoJob()
    {
        $queue = $this->prophesize(MongoQueue::class);
        $queue->getName()->willReturn('queue');
        $queueMock = $queue->reveal();


        $logger = $this->prophesize(LoggerInterface::class);
        /* @var LoggerInterface $loggerMock */
        $loggerMock = $logger->reveal();

        $awareJob = $this->prophesize(AbstractJob::class);
        $awareJob->willImplement(LoggerAwareInterface::class);
        $awareJob->getId()->willReturn('jobId');
        $awareJob->setLogger($loggerMock)->shouldBeCalled();

        $event = $this->prophesize(ProcessJobEvent::class);
        $event->getQueue()->willReturn($queueMock);
        $event->getJob()->willReturn($awareJob->reveal());
        $event->setParam('logger', $loggerMock)->willReturn(null);
        /* @var ProcessJobEvent $eventMock */
        $eventMock = $event->reveal();

        $this->target->setLogger($loggerMock);
        $this->target->logJobStart($eventMock);
    }

    public function provideLogJobEndData()
    {
        return [
            [ProcessJobEvent::JOB_STATUS_SUCCESS, 'info', 'SUCCESS'],
            ['some', 'info', 'SUCCESS'],
            [ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE, 'warn', 'RECOVERABLE'],
            [ProcessJobEvent::JOB_STATUS_FAILURE, 'err', 'FAILURE'],
        ];
    }

    /**
     * @dataProvider provideLogJobEndData
     *
     * @param $result
     * @param $method
     * @param $expectContains
     */
    public function testLogsJobEndEvent($result, $method, $expectContains)
    {
        $queue = $this->prophesize(MongoQueue::class);
        $queue->getName()->willReturn('queue');
        $queueMock = $queue->reveal();

        $logger = $this->prophesize(LoggerInterface::class);

        $job = $this->prophesize(AbstractJob::class);
        $job->getId()->willReturn('jobId');

        if ('info' !== $method) {
            $logger->$method(Argument::allOf(
                Argument::containingString($expectContains),
                Argument::containingString('jobId')
            ))->shouldBeCalled();
        } else {
            $logger->$method(Argument::allOf(
                Argument::containingString($expectContains),
                Argument::containingString('jobId')
            ))->shouldBeCalled();
        }
        /* @var LoggerInterface $loggerMock */
        $loggerMock = $logger->reveal();

        $event = $this->prophesize(ProcessJobEvent::class);
        $event->getQueue()->willReturn($queueMock);
        $event->getJob()->willReturn($job->reveal());
        $event->getResult()->willReturn($result);
        /* @var ProcessJobEvent $eventMock */
        $eventMock = $event->reveal();

        $this->target->setLogger($loggerMock);
        $this->target->logJobEnd($eventMock);
    }
}
