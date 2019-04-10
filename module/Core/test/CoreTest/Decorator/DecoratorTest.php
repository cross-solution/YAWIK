<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Decorator;

use PHPUnit\Framework\TestCase;

use Core\Decorator\Decorator;

/**
 * Tests for \Core\Decorator\Decorator
 *
 * @covers \Core\Decorator\Decorator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Decorator
 */
class DecoratorTest extends TestCase
{
    public function testConstruction()
    {
        $object = new \stdClass();
        $target = new Decorator($object);

        $this->assertAttributeEquals($object, 'object', $target);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrapped entity must be of type \stdClass
     */
    public function testConstructionThrowsErrorIfObjectTypeIsNotValid()
    {
        new Decorator(array());
    }
}
