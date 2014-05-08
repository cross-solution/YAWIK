<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AbstractProfile.php */ 
namespace Auth\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * 
 * @ODM\EmbeddedDocument
 */
class Profile extends AbstractEntity implements ProfileInterface
{
    /**
     * 
     * @var unknown
     * @ODM\String
     */
    protected $name;
    
    /**
     * 
     * @var unknown
     * @ODM\Hash
     */
    protected $data;
    
    public function __construct($name = null, array $data = array())
    {
        $this->setName($name);
        $this->setData($data);
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function __get($property)
    {
        try {
            return parent::__get($property);
        } catch (\OutOfBoundsException $e) {
            return $this->get($property);
        }
    }
    
    public function __set($property, $value)
    {
        try {
            return parent::__set($property, $value);
        } catch (\OutOfBoundsException $e) {
            return $this->set($property, $value);
        }
    }
    
    
    
}

