<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Core\Entity;

use Core\Entity\Exception\OutOfBoundsException;

/**
 * Implementation of \Core\Entity\EntityInterface.
 *
 *
 * @var EntityInterface $this
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
trait EntityTrait
{
    public function notEmpty($property, array $args=[])
    {
        $method = "get$property";

        if (!method_exists($this, $method)) {
            throw new OutOfBoundsException("'$property' is not a valid property of '" . get_class($this) . "'");
        }

        $value = count($args)
            ? call_user_func_array([ $this, $method ], $args)
            : $this->$method();

        if (null === $value) { // is_scalar does not consider 'null' to be scalar value.
            return false;
        }

        if (is_scalar($value) || is_array($value)) {
            return !empty($value);
        }

        if (is_resource($value)) {
            return true;
        }

        /*
         * $value must be an object.
         */
        if ($value instanceof \Countable) {
            return (bool) count($value);
        }

        return true;
    }

    public function hasProperty($property, $mode = self::PROPERTY_STRICT)
    {
        $hasProperty = property_exists($this, $property);

        if (!$hasProperty || self::PROPERTY_FACILE === $mode) {
            return $hasProperty;
        }

        $hasGetter = method_exists($this, "get$property");

        if (self::PROPERTY_GETTER === $mode) {
            return $hasGetter;
        }

        $hasSetter = method_exists($this, "set$property");

        if (self::PROPERTY_SETTER === $mode) {
            return $hasSetter;
        }

        return $hasGetter && $hasSetter;
    }
}
