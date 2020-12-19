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
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Util\InvalidArgumentHelper;
use CoreTestUtils\Constraint\ExtendsOrImplements;

/**
 * Provide methods for inheritance assertion.
 *
 * @see    ExtendsOrImplements
 *
 * @method assertThat()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0,26
 */
trait AssertInheritanceTrait
{
    /**
     * Assert that an object extends or implements specific classes resp. interfaces.
     *
     * @param string[] $parentsAndInterfaces
     * @param object   $object
     * @param string   $message
     *
     * @throws PHPUnitException
     */
    public static function assertInheritance($parentsAndInterfaces, $object, $message = '')
    {
        if (!is_object($object)) {
            throw InvalidArgumentHelper::factory(2, 'object');
        }
        self::assertThat($object, self::extendsOrImplements($parentsAndInterfaces), $message);
    }

    /**
     * Creates and returns an ExtendsOrImplements constraint.
     *
     * @param string[] $parentsAndInterfaces
     *
     * @return ExtendsOrImplements
     * @throws PHPUnitException
     */
    public static function extendsOrImplements($parentsAndInterfaces)
    {
        if (!is_array($parentsAndInterfaces)) {
            throw InvalidArgumentHelper::factory(
                1,
                'array or ArrayAccess'
            );
        }

        return new ExtendsOrImplements($parentsAndInterfaces);
    }
}
