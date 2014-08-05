<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
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
    
    
    /**
     * Sets a property through the setter method.
     * 
     * An exception is raised, when no setter method exists.
     * 
     * @param string $property
     * @param mixed $value
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __set($property, $value)
    {
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
     * @param string $property
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __get($property)
    {
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
     * @param string $name
     * @return boolean
     */
    public function __isset($property)
    {
        try {
            $value = $this->__get($property);
        } catch (\OutOfBoundsException $e) {
            return false;
        }
        
        if (is_array($value) && !count($value)) {
            return false;
        }
        if (is_bool($value) || is_object($value)) {
            return true;
        }
        return (bool) $value;
    }
    
   
   
}