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

/**
 * Inheritance test.
 *
 * Classes (TestCases) uses this trait can assert inheritance and interface implementations
 * simply by defining a $target and $inheritance property.
 *
 * Property $target should contain the class name or the SUT instance.
 * NOTE: If the class using this trait defines its own setup() method, you need to populate the
 * $target property!
 *
 * Property $inheritance should be an array with class and/or interface names the $target should be
 * extending from or implementing.
 *
 *
 * @property $target
 * @property $inheritance
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 */
trait TestInheritanceTrait
{
    use SetupTargetTrait, AssertInheritanceTrait;

    /**
     * @testdox Extends correct parent and implements required interfaces.
     * @coversNothing
     */
    public function testInheritance()
    {
        $errTmpl = __TRAIT__ . ': ' . get_class($this);

        if (!property_exists($this, 'inheritance') || !property_exists($this, 'target')) {
            throw new \PHPUnit_Framework_Exception($errTmpl . ' must define the properties "$inheritance" and "$target"');
        }

        if (!is_array($this->inheritance)) {
            throw new \PHPUnit_Framework_Exception($errTmpl . ': Property $inheritance must be an array');
        }

        if (!is_object($this->target)) {
            throw new \PHPUnit_Framework_Exception($errTmpl . ': Property $target must be an object');
        }

        $this->assertInheritance($this->inheritance, $this->target);
    }
}
