<?php
/**
 * YAWIK
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
    protected $_module;
    
    public function __construct($module = null)
    {
        if (null == $this->module) {
            if (null == $module) {
                $module = $this->detectModuleName();
            
                if (null == $module) {
                    throw new \InvalidArgumentException('Module name is required.');
                }
            }
            
            $this->_module = $module;
        } else {
            
            throw new \InvalidArgumentException('Module name is immutable, once it is set.');
        }

    }
    
    public function getModuleName()
    {
        return $this->_module;
    }
    
    public function enableWriteAccess()
    {
        return parent::enableWriteAccess(true, array('module'));
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

}

