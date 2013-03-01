<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Model;


/**
 *
 */
abstract class AbstractModel implements ModelInterface
{
    public $id;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setData(array $data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
    
    public function __set($name, $value)
    {
        $method = "set$name";
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        
        throw new \OutOfBoundsException("'$name' is not a valid property of '" . get_class($this). "'");
    }
    
    public function __get($name)
    {
        $method = "get$name";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        throw new \OutOfBoundsException("'$name' is not a valid property if '" . get_class($this) . "'");
    }
}