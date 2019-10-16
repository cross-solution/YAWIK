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

use Applications\Entity\Subscriber;

/**
 * Tests for Subscriber
 *
 * @covers \Applications\Entity\Subscriber
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class SubscriberTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Subscriber
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Subscriber();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\Subscriber
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\Subscriber', $this->target);
    }

    public function testSetGetUri()
    {
        $uri="http://test.de/test";
        $this->target->setUri($uri);
        $this->assertEquals($this->target->getUri(), $uri);
    }

    public function testSetGetName()
    {
        $name="myPersonalYawik";
        $this->target->setName($name);
        $this->assertEquals($this->target->getName(), $name);
    }
}
