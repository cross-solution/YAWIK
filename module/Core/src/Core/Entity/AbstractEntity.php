<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
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
abstract class AbstractEntity extends AbstractAnonymEntity implements EntityInterface
{ 
    
    
    /**
     * Sets a property through the setter method.
     * 
     * An exception is raised, when no setter method exists.
     * 
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __set($name, $value)
    {
        $method = "set$name";
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        
        throw new OutOfBoundsException("'$name' is not a valid property of '" . get_class($this). "'");
    }
    
    /**
     * Gets a property through the getter method.
     * 
     * An exception is raised, when no getter method exists.
     * 
     * @param string $name
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function __get($name)
    {
        $method = "get$name";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        throw new OutOfBoundsException("'$name' is not a valid property of '" . get_class($this) . "'");
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
    public function __isset($name)
    {
        try {
            $value = $this->__get($name);
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