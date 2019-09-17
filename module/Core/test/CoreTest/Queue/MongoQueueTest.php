<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue;

use PHPUnit\Framework\TestCase;

use Core\Queue\MongoQueue;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SlmQueue\Job\AbstractJob;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\AbstractQueue;

/**
 * Tests for \Core\Queue\MongoQueue
 *
 * @covers \Core\Queue\MongoQueue
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoQueueTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|MongoQueue|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        MongoQueue::class,
        'setupArgs',
        '@testInheritance' => [ 'as_reflection' => true ],
        '@testConstruction' => false,
        '@testPushLazyWithStringName' => '#mockPush',
        '@testPushLazyWithArraySpec' => '#mockPush',
        '#mockPush' => [
            'mock' => ['push' => 1]
        ]
    ];

    private $inheritance = [ AbstractQueue::class ];

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mongoCollectionMock;
    /**
     *
     *
     * @var string
     */
    private $queueName;
    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $jobManagerMock;

    /**
     *
     *
     * @var string
     */
    private $testJobClass;

    private function setupArgs()
    {
        $this->testJobClass = get_class(new class extends AbstractJob {
            public function __construct($id = null, $content = null)
            {
                if ($id) {
                    $this->setId($id);
                }
                if ($content) {
                    $this->setContent($content);
                }
            }
            public function execute()
            {
            }
        });

        $this->mongoCollectionMock = $this->getMockBuilder(\MongoDB\Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['insertOne', 'findOneAndUpdate', 'find', 'deleteOne'])
            ->getMock();


        $this->queueName = 'test-queue';

        $this->jobManagerMock = $this->getMockBuilder(JobPluginManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has', 'build'])
            ->getMock();

        return [$this->mongoCollectionMock, $this->queueName, $this->jobManagerMock];
    }

    public function testConstruction()
    {
        /* @var \MongoDB\Collection|\PHPUnit_Framework_MockObject_MockObject $collection */
        /* @var \SlmQueue\Job\JobPluginManager|\PHPUnit_Framework_MockObject_MockObject $manager */
        $collection = $this->getMockBuilder(\MongoDB\Collection::class)->disableOriginalConstructor()->getMock();
        $name = 'test-queue';
        $manager = $this->getMockBuilder(JobPluginManager::class)->disableOriginalConstructor()->getMock();

        $target = new MongoQueue($collection, $name, $manager);

        $this->assertAttributeSame($collection, 'mongoCollection', $target);
    }

    public function testPushJobToQueue()
    {
        $job = new $this->testJobClass();
        $resultMock = $this->getMockBuilder(\MongoDB\InsertOneResult::class)->disableOriginalConstructor()
            ->setMethods(['getInsertedId'])->getMock();
        $resultMock->expects($this->once())->method('getInsertedId')->willReturn('ID');
        $this->mongoCollectionMock->expects($this->once())->method('insertOne')
            ->with($this->callback(function ($value) {
                return isset($value['queue']) && $value['queue'] == $this->queueName
                    && isset($value['status']) && $value['status'] == MongoQueue::STATUS_PENDING
                    && !isset($value['tried'])
                    && !isset($value['message']) && !isset($value['trace'])
                    && isset($value['created']) && isset($value['scheduled'])
                    && $value['created'] == $value['scheduled']
                    && $value['created'] instanceof \MongoDB\BSON\UTCDateTime
                    && isset($value['priority']) && $value['priority'] == MongoQueue::DEFAULT_PRIORITY
                ;
            }))
            ->willReturn($resultMock);

        $this->target->push($job);

        $this->assertEquals('ID', $job->getId());
    }

    public function testPushJobToQueueWithOptions()
    {
        $job = new $this->testJobClass();
        $options = [
            'priority' => 10,
            'delay' => 100,
        ];
        $resultMock = $this->getMockBuilder(\MongoDB\InsertOneResult::class)->disableOriginalConstructor()
                           ->setMethods(['getInsertedId'])->getMock();
        $resultMock->expects($this->once())->method('getInsertedId')->willReturn('ID');
        $this->mongoCollectionMock->expects($this->once())->method('insertOne')
                                  ->with($this->callback(function ($value) use ($options) {
                                      return isset($value['queue']) && $value['queue'] == $this->queueName
                                             && isset($value['status']) && $value['status'] == MongoQueue::STATUS_PENDING
                                             && !isset($value['tried'])
                                             && !isset($value['message']) && !isset($value['trace'])
                                             && isset($value['created']) && isset($value['scheduled'])
                                             && $value['scheduled'] instanceof \MongoDB\BSON\UTCDateTime
                                             && $value['created'] instanceof \MongoDB\BSON\UTCDateTime
                                             && (int) ((string) $value['scheduled']) - (int) ((string)$value['created']) == $options['delay'] * 1000
                                             && isset($value['priority']) && $value['priority'] == $options['priority']
                                          ;
                                  }))
                                  ->willReturn($resultMock);

        $this->target->push($job, $options);

        $this->assertEquals('ID', $job->getId());
    }

    public function testRetryJob()
    {
        /* @var AbstractJob $job */
        $job = new $this->testJobClass();
        $job->setId(new \MongoDB\BSON\ObjectID());
        $job->setMetadata('mongoqueue.tries', 9);


        $this->mongoCollectionMock->expects($this->once())->method('findOneAndUpdate')
                                  ->with(
                                      $this->equalTo(['_id' => new \MongoDB\BSON\ObjectID($job->getId())]),
                                      $this->callback(
                                          function ($value) {
                                              if (!array_key_exists('$set', $value)) {
                                                  return false;
                                              }
                                              $value = $value['$set'];
                                              return isset($value['tried']) && $value['tried'] == 10
                                                && !array_key_exists('created', $value)
                                        ;
                                          }
                                  )
                                  )
                                  ->willReturn(null);

        $this->target->retry($job);
    }

    public function testPopJob()
    {
        /* @var AbstractJob $job */
        $job = new $this->testJobClass();
        $job->setId(new \MongoDB\BSON\ObjectID());
        $envelope = [
            '_id' => $job->getId(),
            'data' => $this->target->serializeJob($job)
        ];

        $this->jobManagerMock->expects($this->once())->method('get')->with(get_class($job))->willReturn(new $this->testJobClass());
        $this->mongoCollectionMock->expects($this->once())->method('findOneAndUpdate')
                                  ->with(
                                      $this->callback(function ($value) {
                                          return
                                                    isset($value['queue']) && $value['queue'] == $this->queueName
                                                    && isset($value['status']) && $value['status'] == MongoQueue::STATUS_PENDING
                                                    && isset($value['scheduled']['$lte']) && $value['scheduled']['$lte'] instanceof \MongoDB\BSON\UTCDateTime
                                              ;
                                      }),
                                      $this->callback(
                                          function ($value) {
                                              if (!array_key_exists('$set', $value)) {
                                                  return false;
                                              }
                                              $value = $value['$set'];
                                              return isset($value['status']) && $value['status'] == MongoQueue::STATUS_RUNNING
                                                 && array_key_exists('executed', $value)
                                              ;
                                          }
                                      ),
                                      $this->callback(function ($value) {
                                          return
                                            isset($value['sort']) && $value['sort'] == ['priority' => 1, 'scheduled' => 1]
                                              && isset($value['returnDocument'])&&$value['returnDocument'] = \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER
                                              ;
                                      })
                                  )
                                  ->willReturn($envelope);

        $actualJob = $this->target->pop();

        $this->assertEquals($job, $actualJob);
    }

    public function testPopEmptyQueue()
    {
        $this->mongoCollectionMock->expects($this->once())->method('findOneAndUpdate')
                                  ->willReturn(null);

        $actualJob = $this->target->pop();

        $this->assertNull($actualJob);
    }

    public function testListingJobs()
    {
        /* @var AbstractJob $job1 */
        /* @var AbstractJob $job2 */
        $job1 = new $this->testJobClass('TestJob1');
        $job2 = new $this->testJobClass('TestJob2');
        $filter = [ 'queue' => $this->queueName ];
        $opt    = [ 'sort' => [ 'scheduled' => 1, 'priority' => 1 ] ];
        $envelopes = [
            [
                '_id' => $job1->getId(),
                'queue' => $this->queueName,
                'status' => MongoQueue::STATUS_PENDING,
                'data' => $this->target->serializeJob($job1)
            ],
            [
                '_id' => $job2->getId(),
                'queue' => $this->queueName,
                'status' => MongoQueue::STATUS_PENDING,
                'data' => $this->target->serializeJob($job2)
            ],
        ];
        $this->jobManagerMock->expects($this->exactly(2))->method('get')
            ->with($this->testJobClass)->will($this->returnCallback(function () {
                return new $this->testJobClass();
            }));

        $cursor = new class($envelopes) {
            private $envelopes;
            public $called = 0;

            public function __construct($envelopes)
            {
                $this->envelopes = $envelopes;
            }
            public function toArray()
            {
                $this->called++;
                return $this->envelopes;
            }
        };

        $this->mongoCollectionMock->expects($this->once())->method('find')
            ->with($filter, $opt)
            ->willReturn($cursor);

        $jobs = $this->target->listing();

        $this->assertEquals(1, $cursor->called, "Cursor::toArray should have been called exactly one time, but was called '{$cursor->called}' times'");
        $this->assertTrue(is_array($jobs), 'Return value should have been an array, but is "' . gettype($jobs) . '" instead."');
        $this->assertTrue(2 == count($jobs), 'Only 2 jobs should have been returned, but it were ' . count($jobs));

        $this->assertArrayHasKey('job', $jobs[0]);
        $this->assertEquals($job1, $jobs[0]['job'], 'Job1 is not the same');
        $this->assertArrayHasKey('job', $jobs[1]);
        $this->assertEquals($job2, $jobs[1]['job'], 'Job2 is not the same.');
    }

    public function testListingJobsWithOptions()
    {
        $options = [
            'status' => MongoQueue::STATUS_RUNNING,
            'limit' => 10,
        ];

        $filter = [ 'queue' => $this->queueName, 'status' => $options['status'] ];
        $opt    = [ 'sort' => [ 'scheduled' => 1, 'priority' => 1 ], 'limit' => $options['limit'] ];

        $this->jobManagerMock->expects($this->never())->method('get');
        $cursor = new class() {
            public function toArray()
            {
                return [];
            }
        };

        $this->mongoCollectionMock->expects($this->once())->method('find')
                                  ->with($filter, $opt)
                                  ->willReturn($cursor);

        $this->target->listing($options);
    }

    public function testFailJob()
    {
        /* @var AbstractJob $job */
        $job = new $this->testJobClass();
        $job->setId(new \MongoDB\BSON\ObjectID());
        $options = ['message' => 'test failure message'];
        $this->mongoCollectionMock->expects($this->once())->method('findOneAndUpdate')
                                  ->with(
                                      $this->equalTo(['_id' => new \MongoDB\BSON\ObjectID($job->getId())]),
                                      $this->callback(
                                          function ($value) use ($options) {
                                              if (!array_key_exists('$set', $value)) {
                                                  return false;
                                              }
                                              $value = $value['$set'];
                                              return isset($value['status']) && $value['status'] == MongoQueue::STATUS_FAILED
                                                 && !array_key_exists('created', $value)
                                                && !array_key_exists('scheduled', $value)
                                              && isset($value['message']) && $value['message'] == $options['message']
                                              ;
                                          }
                                      )
                                  )
                                  ->willReturn(null);

        $this->target->fail($job, $options);
    }

    public function testDeleteJob()
    {
        /* @var AbstractJob $job */
        $job = new $this->testJobClass('test');
        $result = $this->prophesize(\MongoDB\DeleteResult::class);
        $result->getDeletedCount()->willReturn(true)->shouldBeCalled();

        $this->mongoCollectionMock->expects($this->once())->method('deleteOne')
            ->with(['_id' => $job->getId()])->willReturn($result->reveal());

        $actual = $this->target->delete($job);

        $this->assertTrue($actual);
    }

    public function testPushLazyThrowsExceptionIfRequestedJobServiceDoesNotExists()
    {
        $this->jobManagerMock->expects($this->once())->method('has')->with('lazyService')->willReturn(false);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Service name "lazyService"');

        $this->target->pushLazy('lazyService');
    }

    public function testPushLazyWithStringName()
    {
        /* @var AbstractJob $job */
        $job = new $this->testJobClass();
        $this->jobManagerMock->expects($this->once())->method('has')->with('lazyName')->willReturn(true);
        $this->jobManagerMock->expects($this->once())->method('build')
            ->with('lazy', ['name' => 'lazyName', 'options'=>[], 'content' => null])
            ->willReturn($job);

        $this->target->expects($this->once())->method('push')->with($job, []);

        $this->target->pushLazy('lazyName');
    }

    public function testPushLazyWithArraySpec()
    {
        /* @var AbstractJob $job */
        $job = new $this->testJobClass();
        $serviceOptions = [
            'opt1' => 'val1',
        ];
        $payload = 'Test';
        $options = ['some' => 'options'];

        $expectOptions = [
            'name' => 'lazyName',
            'options' => $serviceOptions,
            'content' => $payload,
        ];

        $this->jobManagerMock->expects($this->once())->method('has')->with('lazyName')->willReturn(true);
        $this->jobManagerMock->expects($this->once())->method('build')->with('lazy', $expectOptions)->willReturn($job);

        $this->target->expects($this->once())->method('push')->with($job, $options);

        $this->target->pushLazy(['lazyName', $serviceOptions], $payload, $options);
    }
}
