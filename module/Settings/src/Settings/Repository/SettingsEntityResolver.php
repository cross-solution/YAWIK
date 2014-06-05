<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsEntityResolver.php */ 
namespace Settings\Repository;

use Settings\Entity\SettingsContainer;
use Settings\Entity\InitializeAwareSettingsContainerInterface;
class SettingsEntityResolver
{
    protected $entityMap;
    
    public function __construct($entityMap)
    {
        $this->entityMap = $entityMap;
    }
    
    public function getNewSettingsEntity($module)
    {
        if (!isset($this->entityMap[$module])) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid module name', $module));
        }
        $class = $this->entityMap[$module];
        $reflClass = new \ReflectionClass($class);
        
        if (!in_array('Settings\Entity\ModuleSettingsContainerInterface', $reflClass->getInterfaceNames())) {
            throw new \DomainException(sprintf(
                'Base settings entity %s must implement \Settings\Entity\ModuleSettingsContainerInterface',
                $class
            ));
        }
        
        $instance = $reflClass->newInstance($module);
        if ($instance instanceOf InitializeAwareSettingsContainerInterface) {
            $instance->init();
        }
        return $instance;
    }
}

