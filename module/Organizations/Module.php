<?php
/**
 * YAWIK
 * Organizations Module Bootstrap
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations;

use Core\ModuleManager\ModuleConfigLoader;


/**
 * Bootstrap class of the organizations module
 */
class Module 
{
    /**
     * Loads module specific configuration.
     * 
     * @return array
     */
    public function getConfig()
    {
         return ModuleConfigLoader::load(__DIR__ . '/config');
    }
    
    /**
     * Loads module specific autoloader configuration.
     * 
     * @return array
     */
    public function getAutoloaderConfig()
    {
        
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'OrganizationsTest' => __DIR__ . '/test/' . 'OrganizationsTest'
                ),
            ),
        );
    }
}