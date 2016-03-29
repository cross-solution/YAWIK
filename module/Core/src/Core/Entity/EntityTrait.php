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
        if ($value instanceOf \Countable) {
            return (bool) count($value);
        }

        return true;
    }
}
