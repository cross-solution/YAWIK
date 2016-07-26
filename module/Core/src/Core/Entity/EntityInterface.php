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
    /**#@+
     * Property checking mode:
     *
     * @see hasProperty()
     */

    /**
     * Strict: Property, Setter and Getter must be defined.
     *
     * @var bool
     */
    const PROPERTY_STRICT = true;

    /**
     * Getter: Property and Getter must be defined.
     *
     * @var string
     */
    const PROPERTY_GETTER = 'GETTER';

    /**
     * Setter: Property and Setter must be defined.
     *
     * @var string
     */
    const PROPERTY_SETTER = 'SETTER';

    /**
     * Facile: Only the property must be defined.
     *
     * @var bool
     */
    const PROPERTY_FACILE = false;

    /**#@-*/

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
     * @since 0.25
     */
    public function notEmpty($property, array $args=[]);

    /**
     * Checks, if this entity has a property.
     *
     * The property must be defined, and additionally one of the following condition must be met,
     * dependent on <i>$mode</i>:
     *
     * - self::PROPERTY_STRICT: A getter AND a setter method must be available.
     * - self::PROPERTY_GETTER: Only a getter must be available.
     * - self::PROPERTY_SETTER: Only a setter must be available.
     * - self::PROPERTY_FACILE: No additional conditions.
     *
     * @param string $property
     * @param bool|string $mode
     *
     * @return bool
     * @since 0.25
     */
    public function hasProperty($property, $mode = self::PROPERTY_STRICT);
}
