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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait TestUsesTraitsTrait
{
    use AssertUsesTraitsTrait;

    /**
     * Tests if the target class uses the expected traits.
     *
     * requires a property named "traits" to provide a list of trait names to check against.
     * Does absolutely nothing, if this property does not exists or evaluates to false
     * (e.g. false, null, '0', '', [], etc. )
     *
     * @coversNothing
     */
    public function testUsesTraits()
    {
        if (!property_exists($this, 'traits') || !property_exists($this, 'target')) {
            $this->fail(self::class . ': ' . static::class . ' must define the properties $target and $traits.');
        }

        $this->assertUsesTraits($this->traits, $this->target);
    }
}