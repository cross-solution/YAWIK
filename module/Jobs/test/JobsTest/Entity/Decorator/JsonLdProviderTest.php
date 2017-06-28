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
    ];

    private $inheritance = [ JsonLdProviderInterface::class ];

    private function getJob()
    {
        $job = new Job();
        $organization = new Organization();
        $name = new OrganizationName('test');
        $organization->setOrganizationName($name);
        $job->setOrganization($organization);
        $job->setTitle('Test JsonLdProvider');
        $job->setDatePublishStart(new \DateTime());
        $locations = new ArrayCollection();
        $location = new Location();
        $locations->add($location);
        $job->setLocations($locations);

        return [$job];
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
}