<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace JobsTest\Listener;

use Core\Service\EntityEraser\LoadEvent;
use Jobs\Entity\Job;
use Jobs\Entity\Job as JobEntity;
use Jobs\Listener\LoadExpiredJobsToPurge;
use Jobs\Repository\Job as JobRepository;
use CoreTestUtils\TestCase\FunctionalTestCase;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;

/**
 * Class LoadExpiredJobsToPurgeTest
 *
 * @covers \Jobs\Listener\LoadExpiredJobsToPurge
 * @author Anthonius Munthi <me@itstoni.com>
 * @group Jobs
 * @group Jobs.Listener
 * @package JobsTest\Listener
 */
class LoadExpiredJobsToPurgeTest extends FunctionalTestCase
{
    use ServiceManagerMockTrait;

    public function testInvoke()
    {
        $job = new Job();
        $job->setTitle('Test Expire Job');
        $job->setId('TestId');
        $job->setDateModified(new \DateTime('- 80 days'));
        $job->setDatePublishEnd(new \DateTime('- 80 days'));

        $jobRepo = new class ($job)
        {
            private $job;

            public function __construct($job)
            {
                $this->job = $job;
            }
            public function createQueryBuilder()
            {
                $this->qb = new class ($this->job)
                {
                    private $job;

                    public function __construct($job) { $this->job = $job; $this->called = []; }
                    public function __call($name, $args)
                    {
                        $args = array_map(
                            function ($item) {
                                if (is_object($item)) { return 'object'; }
                                return $item;
                            },
                            $args
                        );

                        $this->called[] = [$name, $args];
                        return $this;
                    }
                    public function toArray() { return [$this->job]; }
                };
                return $this->qb;
            }
        };

        $event = $this->createMock(LoadEvent::class);
        $event->expects($this->once())
            ->method('getRepository')
            ->with('Jobs')
            ->willReturn($jobRepo);
        $event->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(['days',80],['limit', 0])
            ->willReturnOnConsecutiveCalls(30, 10)
        ;


        $ob = new LoadExpiredJobsToPurge();
        $entities = $ob->__invoke($event);

        $this->assertIsArray($entities);
        $this->assertEquals($job, array_pop($entities));

        $expectedQbCalls = [
            ['field', ['status.name']],
            ['notEqual', ['active']],
            ['expr', []],
            ['expr', []],
            ['field', ['datePublishEnd']],
            ['exists', ['1']],
            ['expr', []],
            ['field', ['datePublishEnd.date']],
            ['lt', ['object']],
            ['addAnd', ['object', 'object']],
            ['expr', []],
            ['expr', []],
            ['field', ['datePublishEnd']],
            ['exists', ['']],
            ['expr', []],
            ['field', ['dateModified.date']],
            ['lt', ['object']],
            ['addAnd', ['object', 'object']],
            ['addOr', ['object', 'object']],
            ['limit', ['10']],
            ['getQuery', []],
            ['execute', []],
        ];
        $this->assertEquals($expectedQbCalls, $jobRepo->qb->called);

    }
}
