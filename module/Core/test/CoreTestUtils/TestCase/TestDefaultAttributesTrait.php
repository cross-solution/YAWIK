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

use PHPUnit\Framework\TestCase;

/**
 * Tests the target for default attribute values.
 *
 * Expects a property names $target to be the SUT (Subject under test) and
 * a property named $attributes to hold the map in the form of
 * <pre>
 * [
 *      'propertyName' => propertyValue,
 *      ...
 * ]
 * </pre>
 *
 * If you need to use expressions (Which are not allowed in attribute definitions in PHP), you
 * can redefine the method getDefaultAttributes() which then should return the attributes map.
 *
 * @property array $attributes
 * @property object $target
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
trait TestDefaultAttributesTrait
{
    use AssertDefaultAttributesValuesTrait;

    /**
     * @testdox Defines correct default attribute values.
     * @coversNothing
     */
    public function testDefaultAttributes()
    {
        if (!property_exists($this, 'target') || !is_object($this->target)) {
            throw new \PHPUnit_Framework_Exception(
                self::class . ': ' . static::class
                . ' must define the property $target and its value must be an object.'
            );
        }


        $attributes = $this->getDefaultAttributes();

        if (!is_array($attributes)) {
            throw new \PHPUnit_Framework_Exception(
                self::class . ': ' . static::class . ': Invalid format of attributes. Must be an array of attribute => value pairs.'
            );
        }

        $this->assertDefaultAttributesValues($attributes, $this->target);
    }

    private function getDefaultAttributes()
    {
        if (!property_exists($this, 'attributes')) {
            throw new \PHPUnit_Framework_Exception(
                self::class . ': ' . static::class
                . ' must define the property $attributes with an array of attribute => value pairs.'
            );
        }

        return $this->attributes;
    }
}
