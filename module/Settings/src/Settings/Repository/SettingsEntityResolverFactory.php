<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsEntityResolverFactory.php */ 
namespace Settings\Repository;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
class SettingsEntityResolverFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleManager = $serviceLocator->get('ModuleManager');
        $config        = $serviceLocator->get('Config');
        $config        = isset($config['settings']) ? $config['settings'] : array();
        
        $map = array();
        foreach (array_keys($moduleManager->getLoadedModules()) as $module) {
            $map[$module] = isset($cfg[$module]['entity'])
                          ? $cfg[$module]['entity']
                          : '\Settings\Entity\ModuleSettingsContainer';
        }
        
        $resolver = new SettingsEntityResolver($map);
        return $resolver;
    }
}

