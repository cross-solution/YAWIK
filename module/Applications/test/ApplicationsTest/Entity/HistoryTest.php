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

use Applications\Entity\History;
use Applications\Entity\Status;

/**
 * Tests for User
 *
 * @covers \Applications\Entity\History
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
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
        $status = new Status();
        $this->target = new History($status, 'message');
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\HistoryInterface
     * @covers \Applications\Entity\History::__construct
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $date = new \DateTime("2017-02-28 11:11:11");
        $this->target->setDate($date);
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\History', $this->target);
        $this->assertEquals($this->target->getDate(), $date);
    }

    /**
     * @dataProvider providerStatusExpected
     * @param $status
     * @param $expected
     */
    public function testConstructWithStatusAsString($status, $expected)
    {
        $target = new History($status, 'message');
        $this->assertEquals($target->getStatus($status), $expected);
    }

    public function providerStatusExpected()
    {
        return [
            [Status::CONFIRMED, new Status(Status::CONFIRMED)],
            [Status::INCOMING, new Status(Status::INCOMING)],
            [Status::INVITED, new Status(Status::INVITED)],
            [Status::REJECTED, new Status(Status::REJECTED)],
        ];
    }

    /**
     * @covers \Applications\Entity\History::setDate
     * @covers \Applications\Entity\History::getDate
     */
    public function testSetGetDate()
    {
        $input=new \DateTime("2012-05-06");
        $this->target->setDate($input);
        $this->assertEquals($this->target->getDate(), $input);
    }

    /**
     * @covers \Applications\Entity\History::setStatus
     * @covers \Applications\Entity\History::getStatus
     * @dataProvider providerStatus
     */
    public function testSetGetStatus($status)
    {
        $input=new Status($status);
        $this->target->setStatus($input);
        $this->assertEquals($this->target->getStatus(), $input);
    }

    public function providerStatus()
    {
        return [
            [Status::CONFIRMED],
            [Status::INCOMING],
            [Status::INVITED],
            [Status::REJECTED],
        ];
    }

    /**
     * @covers \Applications\Entity\History::setMessage
     * @covers \Applications\Entity\History::getMessage
     */
    public function testSetGetMessage()
    {
        $input="this is my message";
        $this->target->setMessage($input);
        $this->assertEquals($this->target->getMessage(), $input);
    }

    /**
     * @covers \Applications\Entity\History::preUpdate
     */
    public function testPreUpdate()
    {
        $this->target->preUpdate();
        $this->assertEquals($this->target->getDate()->format('dmY H:i:s'), (new \DateTime())->format('dmY H:i:s'));
    }
}
