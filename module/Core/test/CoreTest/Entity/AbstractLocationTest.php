<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Entity;

use Core\Entity\AbstractLocation;
use GeoJson\Geometry\Point;

class ConcreteLocation extends AbstractLocation
{

}

/**
 * Class AbstractLocationTest
 *
 * @package CoreTest\Entity
 */
class AbstractLocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var ConcreteLocation
     */
    private $target;

    public function setup()
    {
        $this->target = new ConcreteLocation();
    }

    /**
     * @testdox Extends \Core\Entity\LocationEntity and implements \Core\Entity\LocationInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsAtsModeInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Core\Entity\LocationInterface', $this->target);
    }


    /**
     * @testdox Allows setting and getting the URI
     */
    public function testSettingAndGettingPostalCode()
    {
        $postalCode = '60486';

        $this->target->setPostalCode($postalCode);

        $this->assertEquals($postalCode, $this->target->getPostalCode());
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