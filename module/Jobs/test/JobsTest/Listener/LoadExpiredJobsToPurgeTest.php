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

    public function testInvoke()
    {
        /* @var JobRepository $jobRepo */
        $sl = $this->getApplicationServiceLocator();
        $jobRepo = $sl->get('repositories')->get(Job::class);

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


        $job = $this->getTestJob($jobRepo);
        $job->setDateModified(new \DateTime('- 40 days'));
        $job->setDatePublishEnd(new \DateTime('- 40 days'));
        $jobRepo->store($job);

        $ob = new LoadExpiredJobsToPurge();
        $entities = $ob->__invoke($event);

        $this->assertIsArray($entities);
        $this->assertEquals($job, array_pop($entities));
    }

    /**
     * @param JobRepository $repo
     * @return JobEntity
     */
    private function getTestJob(JobRepository $repo)
    {
        $title = 'Test Expired Job';
        $job = $repo->findOneBy(['title' => $title]);
        if(!$job instanceof JobEntity){
            $job = $repo->create(['title' => $title],true);
        }

        return $job;
    }
}
