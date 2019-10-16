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

use Applications\Entity\Status;
use Applications\Entity\StatusInterface;

/**
 * Tests for Status
 *
 * @covers \Applications\Entity\Status
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
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
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\Subscriber
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\Status', $this->target);
        $this->assertInstanceOf('\Applications\Entity\StatusInterface', $this->target);
    }

    /**
     * @dataProvider providerApplicationStatus
     */
    public function testGetName($input, $expected)
    {
        $status = new Status($input);
        $this->assertEquals($status->getName(), $expected);
    }

    public function providerApplicationStatus()
    {
        return
            [
                [StatusInterface::INCOMING, StatusInterface::INCOMING],
                [StatusInterface::ACCEPTED, StatusInterface::ACCEPTED],
                [StatusInterface::CONFIRMED,StatusInterface::CONFIRMED],
                [StatusInterface::INVITED,  StatusInterface::INVITED],
                [StatusInterface::REJECTED, StatusInterface::REJECTED],

            ];
    }

    /**
     * @dataProvider providerApplicationStatusOrder
     */
    public function testGetOrder($input, $expected)
    {
        $status = new Status($input);
        $this->assertEquals($status->getOrder(), $expected);
    }

    public function providerApplicationStatusOrder()
    {
        return
            [
                [StatusInterface::INCOMING, 10],
                [StatusInterface::ACCEPTED, 25],
                [StatusInterface::CONFIRMED, 20],
                [StatusInterface::INVITED, 30],
                [StatusInterface::REJECTED, 40],
            ];
    }

    /**
     * @expectedException     \DomainException
     * @expectedExceptionMessage Unknown status: unknown status
     */
    public function testGetUnknownStatus()
    {
        $expected="foobar";
        $status = new Status("unknown status");
        $this->assertEquals($status->getOrder(), $expected);
    }

    public function testTheOrderOfStates()
    {
        $expected = [
            StatusInterface::INCOMING,
            StatusInterface::CONFIRMED,
            StatusInterface::ACCEPTED,
            StatusInterface::INVITED,
            StatusInterface::REJECTED
        ];
        $states = $this->target->getStates();
        $this->assertEquals($states, $expected);
    }
}
