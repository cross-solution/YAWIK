<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsEntityResolver.php */ 
namespace Settings\Repository;

use Settings\Entity\SettingsContainer;
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
        
        return $reflClass->newInstance($module);
    }
}

