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
 *
 * @property object|string $target
 * @property array $inheritance
 * @method fail
 * @method assertInstanceOf
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,25
 */
trait AssertInheritanceTrait
{
    use SetupTargetTrait;

    /**
     * @testdox Extends correct parent and implements required interfaces.
     * @coversNothing
     */
    public function testInheritance()
    {
        $errTmpl = __TRAIT__ . ': ' . get_class($this);

        if (!property_exists($this, 'inheritance') || !property_exists($this, 'target')) {
            $this->fail($errTmpl . ' must define the properties "$inheritance" and "$target"');
        }

        if (!is_array($this->inheritance)) {
            $this->fail($errTmpl . ': Property $inheritance must be an array');
        }

        if (!is_object($this->target)) {
            $this->fail($errTmpl . ': Property $target must be an object');
        }

        foreach ($this->inheritance as $class) {
            $this->assertInstanceOf(
                 $class,
                 $this->target,
                 $errTmpl . ': ' . get_class($this->target) . ' does not '
                          . (false !== strpos($class, 'Interface') ? 'implement' : 'extend')
                          . ' ' . $class
            );
        }
    }
}