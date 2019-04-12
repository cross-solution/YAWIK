<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue\Worker;

use PHPUnit\Framework\TestCase;

use Core\Queue\Exception\FatalJobException;
use Core\Queue\Exception\RecoverableJobException;
use Core\Queue\MongoQueue;
use Core\Queue\Worker\MongoWorker;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Prophecy\Argument;
use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\AbstractQueue;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * Tests for \Core\Queue\Worker\MongoWorker
 *
 * @covers \Core\Queue\Worker\MongoWorker
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoWorkerTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|MongoWorker|\ReflectionClass
     */
    private $target = [
        MongoWorker::class,
        'setupConstructorArgs',
        '@testInheritance' => [ 'as_reflection' => true ]
    ];

    private $eventsMock;

    private $inheritance = [ AbstractWorker::class ];

    private function setupConstructorArgs()
    {
        $this->eventsMock = $this->createMock(EventManagerInterface::class);

        return [$this->eventsMock];
    }

    public function testDoesNotProcessJobsIfQueueIsNotMongoQueue()
    {
        /* @var AbstractJob $jobMock */
        $job = $this->prophesize(AbstractJob::class);
        $job->execute()->shouldNotBeCalled();
        $jobMock = $job->reveal();


        /* @var MongoQueue $queue */
        $queue = $this->createMock(AbstractQueue::class);

        $this->target->processJob($jobMock, $queue);
    }

    public function testDoesDeleteJobIfProcessedSuccessfully()
    {
        /* @var AbstractJob $jobMock */
        $job = $this->prophesize(AbstractJob::class);
        $job->execute()->willReturn(ProcessJobEvent::JOB_STATUS_SUCCESS);
        $jobMock = $job->reveal();

        /* @var MongoQueue $queueMock */
        $queue = $this->prophesize(MongoQueue::class);
        $queueMock = $queue->reveal();

        $result = $this->target->processJob($jobMock, $queueMock);

        static::assertEquals(ProcessJobEvent::JOB_STATUS_SUCCESS, $result);
    }

    public function testDoesRetryJobIfEncounteredRecoverableFailure()
    {
        $options = ['delay' => 10];

        /* @var AbstractJob $jobMock */
        $job = $this->prophesize(AbstractJob::class);
        $job->execute()->willThrow(new RecoverableJobException('test recoverable', $options));
        $jobMock = $job->reveal();

        /* @var MongoQueue $queueMock */
        $queue = $this->prophesize(MongoQueue::class);
        $queueMock = $queue->reveal();

        $result = $this->target->processJob($jobMock, $queueMock);
        static::assertEquals(ProcessJobEvent::JOB_STATUS_FAILURE, $result);
    }

    public function testDoesFailJobIfEncounteredFatalFailure()
    {
        $options = ['delay' => 10];

        /* @var AbstractJob $jobMock */
        $job = $this->prophesize(AbstractJob::class);
        $job->execute()->willThrow(new FatalJobException('test fatal', $options));
        $jobMock = $job->reveal();

        /* @var MongoQueue $queueMock */
        $queue = $this->prophesize(MongoQueue::class);
        $queueMock = $queue->reveal();

        $result = $this->target->processJob($jobMock, $queueMock);
        static::assertEquals(ProcessJobEvent::JOB_STATUS_FAILURE, $result);
    }
}
