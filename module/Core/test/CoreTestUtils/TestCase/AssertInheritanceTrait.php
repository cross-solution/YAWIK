<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTestUtils\TestCase;

use CoreTestUtils\Constraint\ExtendsOrImplements;

/**
 * Inheritance assertions.
 *
 * Classes (TestCases) uses this trait can assert inheritance and interface implementations
 * simply by defining a $target and $inheritance property.
 *
 * Property $target should contain the class name or the SUT instance.
 * NOTE: If the class using this trait defines its own setup() method, you need to populate the
 * $target property!
 *
 * Property $inheritance should be an array with class and/or interface names the $target shoul be
 * extending from or implementing. Each of this names will get asserted by calling ::assertInstanceOf()
 *
 * @method assertThat()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,25
 */
trait AssertInheritanceTrait
{
    public static function assertInheritance($parentsAndInterfaces, $object, $message = '')
    {
        if (!is_object($object)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(2, 'object');
        }
        self::assertThat($object, self::extendsOrImplements($parentsAndInterfaces), $message);
    }

    public static function extendsOrImplements($parentsAndInterfaces)
    {

        if (!is_array($parentsAndInterfaces)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(
                                                    1,
                                                    'array or ArrayAccess'
            );
        }

        return new ExtendsOrImplements($parentsAndInterfaces);
    }
}