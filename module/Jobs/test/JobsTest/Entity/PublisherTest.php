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

use Jobs\Entity\Publisher;
use GeoJson\GeoJson;

/**
 * Tests for Publisher
 *
 * @covers \Jobs\Entity\Publisher
 * @coversDefaultClass \Jobs\Entity\Publisher
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class PublisherTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Publisher
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Publisher();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsAtsModeInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
    }

    public function testSetGetHost()
    {
        $input = 'yawik.org';
        $this->target->setHost($input);
        $this->assertEquals($input, $this->target->getHost());
    }

    public function testSetGetReference()
    {
        $input = 'myReference';
        $this->target->setReference($input);
        $this->assertEquals($input, $this->target->getReference());
    }

    public function testSetGetExternalId()
    {
        $input = 'myReference';
        $this->target->setExternalId($input);
        $this->assertEquals($input, $this->target->getExternalId());
    }
}
