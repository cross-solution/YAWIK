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

use Jobs\Entity\Status;
use Jobs\Entity\StatusInterface;

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
class StatusTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Status
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Status();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\StatusInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsStatusInterface()
    {
        $this->assertInstanceOf('\Core\Entity\Status\AbstractSortableStatus', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\StatusInterface', $this->target);
    }

    public function provideCreatingInstancesTestData()
    {
        return array(
            array(Status::CREATED,              Status::CREATED               , 10),
            array(Status::WAITING_FOR_APPROVAL, Status::WAITING_FOR_APPROVAL  , 20),
            array(Status::REJECTED,             Status::REJECTED              , 30),
            array(Status::PUBLISH,              Status::PUBLISH               , 40),
            array(Status::ACTIVE,               Status::ACTIVE                , 50),
            array(Status::INACTIVE,             Status::INACTIVE              , 60),
            array(Status::EXPIRED,              Status::EXPIRED               , 70),
        );
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideCreatingInstancesTestData
     * @covers ::__construct
     * @covers \Jobs\Entity\Status::getName
     * @covers \Jobs\Entity\Status::getOrder
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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid state name:
     */
    public function testStatusThrowsExceptionIfInvalidStatusPassed()
    {
        $target = new Status('highly invalid status name');
    }

    public function testGetStates()
    {
        $expected = [
            Status::CREATED,
            Status::WAITING_FOR_APPROVAL,
            Status::REJECTED,
            Status::PUBLISH,
            Status::ACTIVE,
            Status::INACTIVE,
            Status::EXPIRED
        ];
        $this->assertEquals($expected, $this->target->getStates());
    }

    public function testToString()
    {
        $state = new Status(StatusInterface::INACTIVE);
        $this->assertEquals(StatusInterface::INACTIVE, (string) $state);
    }
}
