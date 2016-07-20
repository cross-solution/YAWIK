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
     *
     * @coversNothing
     */
    public function testDefaultAttributes()
    {
        if (!property_exists($this, 'target') || !is_object($this->target)) {
            throw new \PHPUnit_Framework_Exception(
                self::class . ': ' . static::class
                . ' must define the property $target and its value must be an object.');
        }

        if (!property_exists($this, 'attributes')) {
            throw new \PHPUnit_Framework_Exception(
                self::class . ': ' . static::class
                . ' must define the property $attributes with an array of attribute => value pairs.');
        }

        $this->assertDefaultAttributesValues($this->attributes, $this->target);
    }
    
}