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
class ModuleSettingsContainer extends SettingsContainer implements ModuleSettingsContainerInterface
{
    
    /** @ODM\String */
    protected $module;
    
    protected $isWritable = false;
    
    public function __construct($module = null)
    {
        if (null == $this->module) {
            if (null == $module) {
                $module = $this->detectModuleName();
            
                if (null == $module) {
                    throw new \InvalidArgumentException('Module name is required.');
                }
            }
            
            $this->module = $module;
        } else {
            
            throw new \InvalidArgumentException('Module name is immutable, once it is set.');
        }

    }
    
    public function getModuleName()
    {
        return $this->module;
    }
    
    public function enableWriteAccess()
    {
        $this->isWritable = true;
    }
    
    protected function detectModuleName()
    {
        $refl = new \ReflectionClass($this);
        $namespace = $refl->getNamespaceName();
        $namespace = ltrim($namespace, '\\');
        
        return false === strpos($namespace, 'Settings\\')
               ? substr($namespace, 0, strpos($namespace, '\\'))
               : null;

    }
    
    protected function checkWriteAccess()
    {
        if (!$this->isWritable) {
            throw new \RuntimeException('Write access is forbidden on this settings container.');
        }
    }
    
    public function set($key, $value)
    {
        $this->checkWriteAccess();
        return parent::set($key, $value);
    }    
    
    public function __set($property, $value)
    {
        $this->checkWriteAccess();
        return parent::__set($key, $value);
    }
}

