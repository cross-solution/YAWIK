<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SettingsContainer.php */
namespace Settings\Entity;

use Core\Entity\EntityTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class SettingsContainer implements SettingsContainerInterface
{
    use EntityTrait;

    /** @ODM\Field(type="hash") */
    protected $_settings;
    
    protected $isWritable = false;

    /**
     * @param bool  $recursive
     * @param array $skipMembers
     *
     * @return $this
     */
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

    /**
     * @param null $key
     * @param null $default
     * @param bool $set
     *
     * @return null
     */
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

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->_settings;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->checkWriteAccess();
        $this->_settings[$key] = $value;
        return $this;
    }

    /**
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings(array $settings)
    {
        $this->_settings = $settings;
        return $this;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return $this|null
     */
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

    /**
     * @param $property
     *
     * @return null
     */
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

    /**
     * @param $property
     * @param $value
     */
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
     * @param string $property
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

    /**
     *
     */
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
