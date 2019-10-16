<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace ApplicationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Applications\Entity\Facts;

/**
 * Tests for Subscriber
 *
 * @covers \Applications\Entity\Facts
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class FactsTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Facts
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Facts();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\Facts
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\Facts', $this->target);
    }

    public function testSetGetExpectedSalary()
    {
        $input="1000€";
        $this->target->setExpectedSalary($input);
        $this->assertEquals($this->target->getExpectedSalary(), $input);
    }

    public function testSetGetWillingnessToTravel()
    {
        $input="test";
        $this->target->setWillingnessToTravel($input);
        $this->assertEquals($this->target->getWillingnessToTravel(), $input);
    }
    public function testSetGetEarliestStartingDate()
    {
        $input="1000€";
        $this->target->setEarliestStartingDate($input);
        $this->assertEquals($this->target->getEarliestStartingDate(), $input);
    }
    public function testSetGetDrivingLicense()
    {
        $input="1000€";
        $this->target->setDrivingLicense($input);
        $this->assertEquals($this->target->getDrivingLicense(), $input);
    }
}
