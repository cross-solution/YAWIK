<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Application;
use Core\Entity\AbstractIdentifiableModificationDateAwareEntity;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Test the AbstractIdentifiableModificationDateAwareEntity Entity
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\AbstractIdentifiableModificationDateAwareEntity
 */
class AbstractIdentifiableModificationAwareEntityTest extends TestCase
{
    protected $target;

    protected function setUp(): void
    {
        $this->target = new ConcreteIdentifiableModificationDateAwareEntity();
    }

    /**
     *  @dataProvider providerCreateDate
     */
    public function testSetGetDateCreated($input, $expected)
    {
        $this->target->setDateCreated($input);
        $this->assertEquals($this->target->getDateCreated(), $expected);
    }

    public function providerCreateDate()
    {
        return [
            [ new \DateTime("2010-11-12"), new \DateTime("2010-11-12")],
            ];
    }

    public function testSetGetDateCreatedWithNull()
    {
        $this->target->setDateCreated();
        $this->assertInstanceOf("DateTime", $this->target->getDateCreated());
    }

    /**
     *  @dataProvider providerModifyDate
     */
    public function testSetGetDateModified($input, $expected)
    {
        $this->target->setDateModified($input);
        $this->assertEquals($this->target->getDateModified(), $expected);
    }

    public function providerModifyDate()
    {
        Application::loadDotEnv();
        $timezone = getenv('TIMEZONE');
        $timezone = new \DateTimeZone($timezone);
        return [
            [ new \DateTime("2010-11-12"), new \DateTime("2010-11-12", $timezone)],
            [ "2011-12-13" , new \DateTime("2011-12-13", $timezone)]
        ];
    }

    public function testSetGetDateModifiedWithNull()
    {
        $this->target->setDateModified();
        $this->assertInstanceOf("DateTime", $this->target->getDateModified());
    }
}

class ConcreteIdentifiableModificationDateAwareEntity extends AbstractIdentifiableModificationDateAwareEntity
{
}
