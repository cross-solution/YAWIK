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

use Applications\Entity\Rating;

/**
 * Tests for Subscriber
 *
 * @covers \Applications\Entity\Rating
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class RatingTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Rating
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Rating();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\Facts
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\Rating', $this->target);
    }

    public function testSetGetRating()
    {
        $input="1";
        $this->target->setRating($input);
        $this->assertEquals($this->target->getRating(), $input);
    }
}
