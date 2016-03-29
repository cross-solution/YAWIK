<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core models */
namespace Core\Entity;

/**
 * Model interface
 */
interface EntityInterface
{

    /**
     * Checks, wether a property is not empty.
     *
     * Uses the getter method of the property to fetch its value, passing
     * the $args to the method.
     *
     * A property is considered empty, if its value
     * - is null,
     * - is a scalar and \empty() returns true,
     * - is an empty array, or
     * - is an object implementing \Countable and ::count() is 0.
     *
     * in all other cases the property is considered to be not empty.
     *
     * @param string $property
     * @param array $args Arguments to be passed to the getter method.
     *
     * @return bool
     * @throws \Core\Entity\Exception\OutOfBoundsException if the property does not exists.
     */
    public function notEmpty($property, array $args=[]);
}
