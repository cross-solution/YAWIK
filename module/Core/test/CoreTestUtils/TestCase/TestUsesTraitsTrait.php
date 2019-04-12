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
 * Uses traits test.
 *
 * Classes (TestCases) using this trait can test wether the SUT uses required traits simply
 * by providing a property named "$traits" which holds an array of trait names and a property named
 * "$target" which holds the SUT instance or FQCN.
 *
 * @property object|string $target
 * @property string[]      $traits
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.26
 */
trait TestUsesTraitsTrait
{
    use AssertUsesTraitsTrait;

    /**
     * @testdox Uses required traits.
     * @coversNothing
     */
    public function testUsesTraits()
    {
        if (!property_exists($this, 'traits') || !property_exists($this, 'target')) {
            throw new \PHPUnit_Framework_Exception(self::class . ': ' . static::class
                                                   . ' must define the properties $target and $traits.');
        }

        $this->assertUsesTraits($this->traits, $this->target);
    }
}
