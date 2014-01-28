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
    protected $settings = array();
    
    protected $someSpecialKey;
    
    public function get($key)
    {
        if (!isset($this->settings[$key])) {
            throw new \OutOfBoundsException(sprintf('Unknown settings key "%s"', $key));
        }
        return $this->settings[$key];
    }
    
    public function set($key, $value)
    {
        $this->settings[$key] = $value;
        return $this;
    }
    
    public function __get($property)
    {
        try {
            return parent::__get($property);
        } catch (\Core\Entity\Exception\OutOfBoundsException $e) {
            try {
                return $this->get($property);
            } catch (\OutOfBoundsException $ex) {
                $ex->setPrevious($e);
                throw $ex;
            }
        }
    }
    
    public function __set($property, $value)
    {
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
    
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }
    
    public function toArray()
    {
        return $this->getSettings();
    }
}

