<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace ApplicationsTestTest\Entity;

use Applications\Entity\History;
use Applications\Entity\Status;
use Auth\Entity\Info;
use Zend\Stdlib\DateTime;

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
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\History', $this->target);
    }

    /**
     * @covers \Applications\Entity\History::setDate
     * @covers \Applications\Entity\History::getDate
     */
    public function testSetGetDate()
    {
        $input=new \DateTime("2012-05-06");
        $this->target->setDate($input);
        $this->assertEquals($this->target->getDate(),$input);
    }

    /**
     * @covers \Applications\Entity\History::setStatus
     * @covers \Applications\Entity\History::getStatus
     */
    public function testSetGetStatus()
    {
        $input=new Status();
        $this->target->setStatus($input);
        $this->assertEquals($this->target->getStatus(),$input);
    }

    /**
     * @covers \Applications\Entity\History::setMessage
     * @covers \Applications\Entity\History::getMessage
     */
    public function testSetGetMessage()
    {
        $input="this is my message";
        $this->target->setMessage($input);
        $this->assertEquals($this->target->getMessage(),$input);
    }

}