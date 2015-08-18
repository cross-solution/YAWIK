<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SettingsContainer.php */
namespace Settings\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * @ODM\EmbeddedDocument
 */
class SettingsContainer implements SettingsContainerInterface
{
    
    /** @ODM\Hash */
    protected $_settings;
    
    protected $isWritable = false;
    
    
    public function enableWriteAccess($recursive = true, array $skipMembers = array())
    {
        $this->isWritable = true;
        
        if ($recursive) {
            $skipMembers = array_merge($skipMembers, array('settings', 'isWritable'));
            foreach (get_object_vars($this) as $member => $value) {
                if (in_array($member, $skipMembers)) {
                    continue;
                }
                
                if ($value instanceof SettingsContainerInterface) {
                    $value->enableWriteAccess(true);
                }
            }
        }
        return $this;
    }
    
    public function get($key = null, $default = null, $set = false)
    {
        if (isset($this->_settings[$key])) {
            return $this->_settings[$key];
        }
        if ($set) {
            $this->set($key, $default);
        }
        return $default;
    }
    
    public function getSettings()
    {
        return $this->_settings;
    }
    
    public function set($key, $value)
    {
        $this->checkWriteAccess();
        $this->_settings[$key] = $value;
        return $this;
    }
    
    public function setSettings(array $settings)
    {
        $this->_settings = $settings;
        return $this;
    }
    
    public function __call($method, $params)
    {
        if (preg_match('~^((?:g|s)et)(.*)$~', $method, $match)) {
            $property = lcfirst($match[2]);
            if (property_exists($this, $property)) {
                if ('set' == $match[1]) {
                    $this->$property = $params[0];
                    return $this;
                } else {
                    return $this->$property;
                }
            }
            $value = isset($params[0]) ? $params[0] : null;
            return $this->{$match[1]}($property, $value);
        }
        
        throw new \BadMethodCallException(
            sprintf(
                'Unknown method %s called on %s',
                $method,
                get_class($this)
            )
        );
    }
    
    public function __get($property)
    {
        $getter = "get" . ucfirst($property);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        
        return $this->get($property);
        
    }
    
    public function __set($property, $value)
    {
        $this->checkWriteAccess();
        
        $setter = 'set' . ucfirst($property);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return;
        }
        
        if (property_exists($this, $property)) {
            $this->$property = $value;
            return;
        }
        
        $this->set($property, $value);
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
        $value = $this->__get($property);
        
        if (is_array($value) && !count($value)) {
            return false;
        }
        if (is_bool($value) || is_object($value)) {
            return true;
        }
        return (bool) $value;
    }
    
    protected function checkWriteAccess()
    {
        if (!$this->isWritable) {
            throw new \RuntimeException(
                sprintf(
                    'Write access to %s is not allowed.',
                    get_class($this)
                )
            );
        }
    }
}
