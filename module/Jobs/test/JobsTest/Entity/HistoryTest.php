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

use Jobs\Entity\History;
use Jobs\Entity\Status;

/**
 * Tests for Status
 *
 * @covers \Jobs\Entity\History
 * @coversDefaultClass \Jobs\Entity\History
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var History
     */
    private $target;

    public function setup()
    {
        $this->target = new History("CREATED");
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\HistoryInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsStatusInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\HistoryInterface', $this->target);
    }

    public function provideCreatingInstancesTestData()
    {
        return array(
            array("CREATED",               null       , new Status("CREATED"),              "[System]"),
            array("WAITING_FOR_APPROVAL", 'message2'  , new Status("WAITING_FOR_APPROVAL"), "message2"),
            array("REJECTED",             'message3'  , new Status("REJECTED"),             "message3"),
            array("PUBLISH",              'message4'  , new Status("PUBLISH"),              "message4"),
            array("ACTIVE",               'message5'  , new Status("ACTIVE"),               "message5"),
            array("INACTIVE",             'message6'  , new Status("INACTIVE"),             "message6"),
            array("EXPIRED",              'message7'  , new Status("EXPIRED"),              "message7"),
        );
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideCreatingInstancesTestData
     * @covers ::__construct
     *
     * @param string $status           the status
     * @param string $message          the message
     * @param string $expectedStatus   the expected status
     * @param string $expectedMessage  the expected message
     */
    public function testCreatingInstances($status, $message, $expectedStatus, $expectedMessage)
    {
        $target = null === $message ? new History($status) : new History($status, $message);

        $this->assertAttributeEquals($expectedStatus, 'status', $target);
        $this->assertAttributeEquals($expectedMessage, 'message', $target);
    }


    /**
     * @testdox Status() throws an exception if invalid status is passed.
     * @expectedException DomainException
     * @expectedExceptionMessage Unknown status:
     */
    public function testStatusThrowsExceptionIfInvalidStatusPassed()
    {
        $target = new History('highly invalid status name');
    }
    
    
    /**
    * @covers Jobs\Entity\History::setDate
    * @covers Jobs\Entity\History::getDate
    */
    public function testSetGetDate()
    {
        $date=new \DateTime("2017-02-28 11:11:11");
        $this->target->setDate($date);

        $this->assertEquals($date, $this->target->getDate());
    }
    
    /**
    * @covers Jobs\Entity\History::setMessage
    * @covers Jobs\Entity\History::getMessage
    */
    public function testSetGetMessage()
    {
        $message="my message";
        $this->target->setMessage($message);

        $this->assertEquals($message, $this->target->getMessage());
    }
    
    /**
    * @covers Jobs\Entity\History::setStatus
    * @covers Jobs\Entity\History::getStatus
    */
    public function testSetGetStatus()
    {
        $status=new Status("CREATED");
        $this->target->setStatus($status);
        $this->assertEquals($status, $this->target->getStatus());
    }
}
