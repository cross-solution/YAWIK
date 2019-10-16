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

use PHPUnit\Framework\TestCase;

use Jobs\Entity\Coordinates;
use GeoJson\GeoJson;

/**
 * Tests for Coordinates
 *
 * @covers \Jobs\Entity\Coordinates
 * @coversDefaultClass \Jobs\Entity\Coordinates
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class CoordinatesTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Coordinates
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Coordinates();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\CoordinatesInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsAtsModeInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\CoordinatesInterface', $this->target);
    }


    /**
     * @testdox Type of a coordinate
     */
    public function testSetGetType()
    {
        $input = 'POINT';
        $this->target->setType($input);
        $this->assertEquals($input, $this->target->getType());
    }

    /**
     * @testdox set coordinates
     */
    public function testSetGetCoordinates()
    {
        $input = [50,8];
        $this->target->setCoordinates($input);
        $this->assertEquals($input, $this->target->getCoordinates());
    }

    public function testToJson()
    {
        $this->target->setCoordinates([50, 8]);
        $this->target->setType('POINT');
        $expected = '{"type":"POINT","coordinates":[50,8]}';
        $this->assertEquals($expected, $this->target->toJson());
    }
}
