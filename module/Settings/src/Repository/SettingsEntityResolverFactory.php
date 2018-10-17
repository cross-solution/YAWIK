<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SettingsEntityResolverFactory.php */
namespace Settings\Repository;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SettingsEntityResolverFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $moduleManager = $container->get('ModuleManager');
        $config        = $container->get('Config');
        
        $map = array();
        foreach (array_keys($moduleManager->getLoadedModules()) as $module) {
            $map[$module] = isset($config[$module]['settings']['entity'])
                ? $config[$module]['settings']['entity']
                : '\Settings\Entity\ModuleSettingsContainer';
        }
        
        $resolver = new SettingsEntityResolver($map);
        return $resolver;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    }
}
