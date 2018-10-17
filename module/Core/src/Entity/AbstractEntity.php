<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core Entitys */
namespace Core\Entity;

use Core\Entity\Exception\OutOfBoundsException;

/**
 * Concrete implementation of \Core\Entity\EntityInterface.
 *
 * Provides some magic function for accessing properties
 * as class members, mirroring these calls to the
 * getter and setter methods.
 *
 */
abstract class AbstractEntity implements EntityInterface
{
    use EntityTrait;

    /**
     * Sets a property through the setter method.
     *
     * An exception is raised, when no setter method exists.
     *
     * @deprecated since 0.25 Use setter method directly.
     * @param string $property
     * @param mixed $value
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __set($property, $value)
    {
        trigger_error(
            sprintf(
                'Accessing entity properties is deprecated. Use setter method instead. ( Tried to access "%s" on %s )',
                $property,
                get_class($this)
            ),
            E_USER_DEPRECATED
        );
        $method = "set$property";
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        
        throw new OutOfBoundsException("'$property' is not a valid property of '" . get_class($this). "'");
    }
    
    /**
     * Gets a property through the getter method.
     *
     * An exception is raised, when no getter method exists.
     *
     * @deprecated since 0.25 use getter method directly.
     * @param string $property
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __get($property)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        trigger_error(
            sprintf(
                'Accessing entity properties is deprecated. Use getter method instead. ( Tried to access "%s" on %s in %s on line %s )',
                $property,
                get_class($this),
                $trace[0]['file'],
                $trace[0]['line']
            ),
            E_USER_DEPRECATED
        );
        $method = "get$property";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new OutOfBoundsException("'$property' is not a valid property of '" . get_class($this) . "'");
    }
    
    /**
     * Checks if a property exists and has a non-empty value.
     *
     * If the property is an array, the check will return, if this
     * array has items or not.
     *
     * @deprecated since 0.25 Use {@link notEmpty()}
     * @param string $property
     * @return boolean
     */
    public function __isset($property)
    {
        trigger_error(
            sprintf(
                'Using isset() with entity properties is deprecated. Use %s::notEmpty("%s") instead.',
                get_class($this),
                $property
            ),
            E_USER_DEPRECATED
        );
        return $this->notEmpty($property);
    }
}
