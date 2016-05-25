<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Entity;

use Core\Entity\Collection\ArrayCollection;
use Jobs\Entity\Location;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point;

/**
 * Tests for Location
 *
 * @covers \Jobs\Entity\Location
 * @coversDefaultClass \Jobs\Entity\Location
 *
 * @author Carsten Bleek <gelhausen@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class LocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Location
     */
    private $target;

    public function setup()
    {
        $this->target = new Location();
    }

    /**
     * @testdox Extends \Core\Entity\LocationEntity and implements \Jobs\Entity\LocationInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsAtsModeInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\LocationInterface', $this->target);
    }


    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingPostalCode()
    {
        $postalCode = '60486';

        $this->target->setPostalcode($postalCode);

        $this->assertEquals($postalCode, $this->target->getPostalcode());
    }

    /**
     * @testdox Allows setting and getting the email address
     */
    public function testSettingAndGettingTheCity()
    {
        $city = 'Frankfurt am Main';

        $this->target->setCity($city);

        $this->assertEquals($city, $this->target->getCity());
    }

    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingCountry()
    {
        $country = 'Deutschland';

        $this->target->setCountry($country);

        $this->assertEquals($country, $this->target->getCountry());
    }

    /**
     * @testdox Allows setting and getting the email address
     */
    public function testSettingAndGettingTheRegion()
    {
        $region = 'Hessen';

        $this->target->setRegion($region);

        $this->assertEquals($region, $this->target->getRegion());
    }

    /**
     * @testdox Allows setting and getting the email address
     */
    public function testSettingAndGettingTheCoordinates()
    {
        $coordinates = new Point([50, 8]);

        $this->target->setCoordinates($coordinates);

        $this->assertEquals($coordinates, $this->target->getCoordinates());
    }
}
