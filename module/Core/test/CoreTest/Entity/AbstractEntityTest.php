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

use Core\Entity\AbstractEntity;

/**
 * Test the Abstract Entity
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\AbstractEntity
 */
class AbstractEntityTest extends TestCase
{
    protected $target;

    protected function setUp(): void
    {
        $this->target = new ConcreteEntity();
    }

    public function testSettingValidAttributes()
    {
        $input = "myValue";
        $this->target->setValidAttribute($input);
        $this->assertSame($this->target->getValidAttribute(), $input);
    }

    public function testGettingValidAttributes()
    {
        $input = "myValue";
        $this->target->setValidAttribute($input);
        $this->assertSame($this->target->getValidAttribute(), $input);
    }

    /**
     * @expectedException \Core\Entity\Exception\OutOfBoundsException
     */
    public function testSettingInvalidAttributes()
    {
        $input = "myValue";
        @$this->target->invalidAttribute=$input;
    }

    /**
     * @expectedException \Core\Entity\Exception\OutOfBoundsException
     */
    public function testGettingInvalidAttributes()
    {
        $input = "myValue";
        $this->assertSame(@$this->target->invalidAttribute, $input);
    }
}

class ConcreteEntity extends AbstractEntity
{
    protected $validAttribute;

    public function setValidAttribute($validAttribute)
    {
        $this->validAttribute=$validAttribute;
        return $this;
    }

    public function getValidAttribute()
    {
        return $this->validAttribute;
    }
}
