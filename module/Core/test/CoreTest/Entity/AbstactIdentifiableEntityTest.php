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

use Core\Entity\AbstractIdentifiableEntity;

/**
 * Test the Identifiable Entity
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\AbstractIdentifiableEntity
 * @covers \Core\Entity\IdentifiableEntityTrait
 */
class AbstractIdentifiableEntityTest extends \PHPUnit_Framework_TestCase
{

    protected $target;

    public function setUp(){
        $this->target = new ConcreteIdentifiableEntity();
    }

    public function testSetGetIdByAttribute(){
        $input = "myValue";
        $this->target->setId($input);
        $this->assertSame($this->target->getId(),$input);
    }

    public function testSetGetIdByMethod(){
        $input = "myValue";
        $this->target->setId($input);
        $this->assertSame($this->target->getId(),$input);
    }
}

class ConcreteIdentifiableEntity extends AbstractIdentifiableEntity {
}
