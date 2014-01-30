<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsContainer.php */ 
namespace Settings\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * @ODM\EmbeddedDocument
 */
class SettingsContainer extends AbstractEntity implements SettingsContainerInterface
{
    
    /** @ODM\Hash */
    protected $settings;
    
    protected $isWritable = false;
    
    
    public function enableWriteAccess($recursive = true, array $skipMembers=array())
    {
        $this->isWritable = true;
        
        if ($recursive) {
            $skipMembers = array_merge($skipMembers, array('settings', 'isWritable'));
            foreach (get_object_vars($this) as $member => $value) {
        
                if (in_array($member, $skipMembers)) {
                    continue;
                }
                
                if ($value instanceOf SettingsContainerInterface) {
                    $value->enableWriteAccess(true);
                }
            }
        }
        return $this;
    }
    
    public function get($key, $default = null, $set = false)
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        if ($set) {
            $this->set($key, $default);
        }
        return $default;
    }
    
    public function set($key, $value)
    {
        $this->checkWriteAccess();
        $this->settings[$key] = $value;
        return $this;
    }
    
    public function __get($property)
    {
        try {
            return parent::__get($property);
        } catch (\Core\Entity\Exception\OutOfBoundsException $e) {
            return $this->get($property);
        }
    }
    
    public function __set($property, $value)
    {
        $this->checkWriteAccess();
        try {
            parent::__set($property, $value);
        } catch (\Core\Entity\Exception\OutOfBoundsException $e) {
            $this->set($property, $value);
        }
    }
    
    public function __isset($property)
    {
        return parent::__isset($property) || isset($this->settings[$property]);
    }
    
    
    public function getSettings()
    {
        return $this->settings;
    }
    
    public function setSettings($settings = null)
    {
        $this->checkWriteAccess();
        if (null !== $settings && !is_array($settings)) {
            throw new \InvalidArgumentException('Settings must be null or an array.');
        }
        
        $this->settings = $settings;
        return $this;
    }
    
    public function toArray()
    {
        return $this->getSettings();
    }
    
    protected function checkWriteAccess()
    {
        if (!$this->isWritable) {
            throw new \RuntimeException('Write access is forbidden on this settings container.');
        }
    }
}

