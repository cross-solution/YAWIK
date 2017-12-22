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

    private function setTargetLocationAttributes()
    {
        $this->target
            ->setStreetname('street')
            ->setStreetnumber('99')
            ->setPostalCode('9999')
            ->setCity('XXXX')
            ->setRegion('YYYY')
            ->setCountry('ZZZZ')
            ->setCoordinates(new Point([10,20]));
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

    public function testStringRepresentation()
    {
        $this->setTargetLocationAttributes();

        $expect = "street 99, 9999 XXXX, YYYY, ZZZZ ( 10, 20 )";

        $this->assertEquals($expect, $this->target->__toString());
    }

    public function testConvertToAndFromJson()
    {
        if (false === (@include_once __DIR__ . '/../../../../Geo/src/Geo/Entity/Geometry/Point.php')) {
            $this->assertTrue(true);
            return;
        }
        $this->setTargetLocationAttributes();

        $expect = json_encode([
            'streetname' => 'street',
            'streetnumber' => '99',
            'city' => 'XXXX',
            'region' => 'YYYY',
            'postalcode' => '9999',
            'country' => 'ZZZZ',
            'coordinates' => [
                    'type' => 'Point',
                    'coordinates' => [10, 20],
            ]

        ]);
        $json = $this->target->toString();
        $this->assertEquals($expect, $json, 'Convert to json did not work');

        $this->target = new ConcreteLocation();
        $this->target->fromString($json);

        $expect = "street 99, 9999 XXXX, YYYY, ZZZZ ( 10, 20 )";

        $this->assertEquals($expect, $this->target->__toString(), 'Convert from json did not work');
    }
}
