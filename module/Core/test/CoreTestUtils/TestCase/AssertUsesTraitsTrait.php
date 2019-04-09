<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\TestCase;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\Constraint\UsesTraits;

/**
 * Trait to be used to easily assert the usage of specific traits of a target class in a test case.
 *
 * @method assertThat()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 */
trait AssertUsesTraitsTrait
{

    /**
     * Asserts that a class uses expected traits.
     *
     * @param array         $traits        Trait names to check against.
     * @param string|object $objectOrClass The target instance or class name
     * @param string        $message       Failure message.
     */
    public static function assertUsesTraits($traits, $objectOrClass, $message = '')
    {
        self::assertThat($objectOrClass, self::usesTraits($traits), $message);
    }

    /**
     * Creates and returns an UsesTraits constraint.
     *
     * @param string[] $traits
     *
     * @return UsesTraits
     * @throws \PHPUnit_Framework_Exception
     */
    public static function usesTraits($traits)
    {
        if (!is_array($traits)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(
                1,
                'array or ArrayAccess'
            );
        }

        return new UsesTraits($traits);
    }
}
