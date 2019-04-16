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

use CoreTestUtils\Constraint\DefaultAttributesValues;

/**
 * Provide methods for default attributes values assertion.
 *
 * @see    DefaultAttributesValues
 *
 * @method assertThat()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0,26
 */
trait AssertDefaultAttributesValuesTrait
{
    /**
     * Assert that an object defines expected attributes and they have the expected value..
     *
     * @param array $defaultAttributes propertyName => value pairs
     * @param object   $object
     * @param string   $message
     *
     * @throws \PHPUnit_Framework_Exception
     */
    public static function assertDefaultAttributesValues($defaultAttributes, $object, $message = '')
    {
        if (!is_object($object)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(2, 'object');
        }
        self::assertThat($object, self::defaultAttributesValues($defaultAttributes), $message);
    }

    /**
     * Creates and returns an DefaultAttributesValues constraint.
     *
     * @param array $defaultAttributes
     *
     * @return DefaultAttributesValues
     * @throws \PHPUnit_Framework_Exception
     */
    public static function defaultAttributesValues($defaultAttributes)
    {
        if (!is_array($defaultAttributes)) {
            throw \PHPUnit_Util_InvalidArgumentHelper::factory(
                1,
                'array or ArrayAccess'
            );
        }

        return new DefaultAttributesValues($defaultAttributes);
    }
}
