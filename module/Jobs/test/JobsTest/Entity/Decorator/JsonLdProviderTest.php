<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Entity\Decorator;

use Core\Entity\Collection\ArrayCollection;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Decorator\JsonLdProvider;
use Jobs\Entity\Job;
use Jobs\Entity\JsonLdProviderInterface;
use Jobs\Entity\Location;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;

/**
 * Tests for \Jobs\Entity\Decorator\JsonLdProvider
 * 
 * @covers \Jobs\Entity\Decorator\JsonLdProvider
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Entity
 * @group Jobs.Entity.Decorator
 */
class JsonLdProviderTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|null|JsonLdProvider
     */
    private $target = [
        JsonLdProvider::class,
        'getJob',
        '@testInheritance' => ['as_reflection' => true],
        '@testConstructSetsJob' => false,
        '@testGeneratesJsonLdWithoutOrganizationAndDate' => [
            'args' => 'getJobWoOrgAndDate'
        ],
    ];

    private $inheritance = [ JsonLdProviderInterface::class ];

    private function getJob($withOrganization=true, $withDatePublishStart=true)
    {
        $job = new Job();
        $organization = new Organization();
        $name = new OrganizationName('test');
        $organization->setOrganizationName($name);
        if ($withOrganization) {
            $job->setOrganization($organization);
        }else{
            $job->setCompany("Company Name");
        }
        $job->setTitle('Test JsonLdProvider');
        if ($withDatePublishStart) { $job->setDatePublishStart(new \DateTime()); }
        $locations = new ArrayCollection();
        $location = new Location();
        $locations->add($location);
        $job->setLocations($locations);

        return [$job];
    }

    private function getJobWoOrgAndDate()
    {
        return $this->getJob(false, false);
    }

    public function testConstructSetsJob()
    {
        $job = new Job();
        $target = new JsonLdProvider($job);

        $this->assertAttributeSame($job, 'job', $target);
    }

    public function testGeneratesJsonLd()
    {

        $json = $this->target->toJsonLd();

        $this->assertContains('"title":"Test JsonLdProvider"', $json);
    }

    public function testGeneratesJsonLdWithoutOrganizationAndDate()
    {
        $json = $this->target->toJsonLd();

        $array = json_decode($json, JSON_OBJECT_AS_ARRAY);
        
        $this->assertTrue(isset($array['hiringOrganization']['name']));
        $this->assertEquals('Company Name', $array['hiringOrganization']['name']);

        $this->assertNull($array['datePosted']);
    }
}
