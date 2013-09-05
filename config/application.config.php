<?php
/**
 * Cross Applicant Management
 * Application configuration
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

return array(
    
    // Activated modules. (Use folder name)
    'modules' => array(
        'Core', /*'TwbBundle', */'Auth', 'Cv', 'Applications', 'Jobs', 'Settings'
    ),
    
    // Where to search modules
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
    
    
        // What configuration files should be autoloaded 
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php'
        ),
    ),
    
    'service_listener_options' => array(
        array(
            'service_manager' => 'MapperManager',
            'config_key'      => 'mappers',
            'interface'       => '\Core\ModuleManager\Feature\MapperProviderInterface',
            'method'          => 'getMapperConfig',      
        ),
        array(
            'service_manager' => 'EntityBuilderManager',
            'config_key'      => 'entity_builders',
            'interface'       => '\Core\ModuleManager\Feature\EntityBuilderProviderInterface',
            'method'          => 'getEntityBuilderConfig',
        ),
        array(
            'service_manager' => 'RepositoryManager',
            'config_key'      => 'repositories',
            'interface'       => '\Core\ModuleManager\Feature\RepositoryProviderInterface',
            'method'          => 'getRepositoryConfig',
        ),
    ),
    
    'service_manager' => array(
        'invokables' => array(
            'MapperManager' => 'Core\Repository\Mapper\MapperManager',
            'EntityBuilderManager' => 'Core\Repository\EntityBuilder\EntityBuilderManager',
            'RepositoryManager' => 'Core\Repository\RepositoryManager',
         ),
         'aliases' => array(
             'mappers' => 'MapperManager',
             'builders' => 'EntityBuilderManager',
             'repositories' => 'RepositoryManager',
         ),
    ),
);