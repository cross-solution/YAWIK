<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Entity;

use Jobs\Entity\Status;

/**
 * Tests for Status
 *
 * @covers \Jobs\Entity\Status
 * @coversDefaultClass \Jobs\Entity\Status
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Status
     */
    private $target;

    public function setup()
    {
        $this->target = new Status();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\StatusInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsStatusInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\StatusInterface', $this->target);
    }

    public function provideCreatingInstancesTestData()
    {
        return array(
            array("CREATED",              Status::CREATED               , 10),
            array("WAITING_FOR_APPROVAL", Status::WAITING_FOR_APPROVAL  , 20),
            array("REJECTED",             Status::REJECTED              , 30),
            array("PUBLISH",              Status::PUBLISH               , 40),
            array("ACTIVE",               Status::ACTIVE                , 50),
            array("INACTIVE",             Status::INACTIVE              , 60),
            array("EXPIRED",              Status::EXPIRED               , 70),
        );
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideCreatingInstancesTestData
     * @covers ::__construct
     * @covers Jobs\Entity\Status::getName
     * @covers Jobs\Entity\Status::getOrder
     *
     * @param string $status           the status to set
     * @param string $expectedName     the expected name for the status
     * @param string $expectedOrder    the expected order for the status
     */
    public function testCreatingInstances($status, $expectedName, $expectedOrder)
    {
        $target = null === $status ? new Status() : new Status($status);

        $this->assertAttributeEquals($expectedName, 'name', $target);
        $this->assertAttributeEquals($expectedOrder, 'order', $target);
    }


    /**
     * @testdox setMode() throws an exception if invalid mode is passed.
     * @expectedException DomainException
     * @expectedExceptionMessage Unknown status:
     */
    public function testStatusThrowsExceptionIfInvalidStatusPassed()
    {
        $target = new Status('highly invalid status name');
    }
}