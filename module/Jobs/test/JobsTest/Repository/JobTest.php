<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace JobsTest\Repository;

use Core\Entity\PermissionsInterface;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentManager;
use Jobs\Entity\Status;
use Jobs\Repository\Job as JobRepository;
use Jobs\Entity\Job;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Test\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;

/**
 * A test for Job Repository
 *
 * @author      Anthonius Munthi <me@itstoni.com>
 * @since       0.26
 * @package     JobsTest\Repository
 * @covers      Jobs\Repository\Job
 */
class JobTest extends AbstractControllerTestCase
{
    /**
     * @var JobRepository
     */
    protected $repository;

    /**
     * @var Job[]
     */
    protected $jobs;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var Organization
     */
    protected $organization;

    public function setUp()
    {
        $this->setApplicationConfig(
            Bootstrap::getConfig()
        );
        parent::setUp();
        $this->repository = $this->getApplicationServiceLocator()
            ->get('repositories')
            ->get('Jobs/Job')
        ;
        $this->dm = $this->getApplicationServiceLocator()
            ->get('doctrine.documentmanager.odm_default')
        ;

        $this->initOrganization();
        $this->initJob();
    }

    protected function initJob()
    {
        $repo = $this->repository;
        $organization = $this->organization;

        for($i=1;$i<=5;$i++){
            $title = 'Job Repository Test '.$i;
            $job = $repo->findOneBy(array('title' => $title));
            if(!$job){
                $job = new Job();
                $job->setTitle($title);
                $job->setStatus(Status::ACTIVE);
                $job->setIsDraft(false);
                $job->setOrganization($organization);
                $this->dm->persist($job);
                $this->dm->flush($job);
            }
            $this->jobs[] = $job;
        }

        $title = 'Job Repository Test Draft';
        $job = $repo->findOneBy(array('title'=>$title));
        if(!$job instanceof Job){
            $job = new Job();
            $job->setTitle($title);
            $job->setIsDraft(true);
            $job->setStatus(Status::CREATED);
            $job->setOrganization($organization);
            $this->dm->persist($job);
            $this->dm->flush($job);
        }
    }

    protected function initOrganization()
    {
        $repo = $this->getApplicationServiceLocator()
            ->get('repositories')
            ->get('Organizations/Organization')
        ;
        $name = 'Job Repository Test Organization';
        $results = $repo->findByName($name);
        $organization = $results[0];

        if(!$organization){
            $organization = new Organization();
            $organization->setIsDraft(false);
            $organization->setOrganizationName(new OrganizationName());
            $organization->getOrganizationName()->setName($name);

            $this->dm->persist($organization);
            $this->dm->flush($organization);
            $this->dm->refresh($organization);
        }
        $this->organization = $organization;
    }

    public function testFindByOrganization()
    {
        $org = $this->organization;
        $result = $this->repository->findByOrganization($org->getId());
        $this->assertCount(6,$result);
    }

    public function testFindActiveOrganizations()
    {
        /* @var Cursor $orgs */
        $orgs = $this->repository->findActiveOrganizations();
        $this->assertCount(1,$orgs);
        $this->assertEquals(
            $this->organization->getName(),
            $orgs->getNext()->getName()
        );
    }

    public function testFindActiveJob()
    {
        $jobs = $this->repository->findActiveJob();
        $this->assertInstanceOf(Cursor::class,$jobs);
        $this->assertCount(5,$jobs);
    }
}
