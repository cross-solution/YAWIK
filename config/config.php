<?php
/**
 * Cross Applicant Management
 * Application configuration
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

$modules = array(
         'Core', /*'TwbBundle', */'Auth', 'Cv', 'Applications', 'Jobs', 'Settings',
    );

if (!isset($allModules)) {
    // allModules existiert nur, damit man verschiedene Konfigurationen auf dem gleichen System laufen lassen kann und über Server-Variablen oder ähnlichen steuern kann
    $allModules = False;
}
foreach (glob(__dir__ . '/autoload/*.module.php') as $moduleFile) {
    $addModules = require $moduleFile;
    foreach ($addModules as $addModule) {
        if (strpos($addModule, '-') === 0) {
            $remove = substr($addModule,1);
            $modules = array_filter($modules, function ($elem) use ($remove) { return strcasecmp($elem,$remove); });
        }
        else {
            if (!in_array($addModule, $modules)) {
                $modules[] = $addModule;
            }
        }
    }
}

return array(

    // Activated modules. (Use folder name)
    'modules' => $modules,
    
    // Where to search modules
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            'Cam\*' => './vendor/extern'
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
        array(
            'service_manager' => 'SettingsManager',
            'config_key'      => 'settings',
            'interface'       => '\Core\ModuleManager\Feature\SettingsProviderInterface',
            'method'          => 'getUserSettings',
        ),
    ),
    
    'service_manager' => array(
        'invokables' => array(
            'MapperManager' => 'Core\Repository\Mapper\MapperManager',
            'EntityBuilderManager' => 'Core\Repository\EntityBuilder\EntityBuilderManager',
            'RepositoryManager' => 'Core\Repository\RepositoryManager',
            'SettingsManager' => 'Core\ModuleManager\SettingsManager',
            //'ServiceListenerInterface' => 'Core\mvc\Service\ServiceListener',
         ),
        'factories' => array(
            //'ServiceListener' => 'Zend\Mvc\Service\ServiceListenerFactory',
            'ServiceListener' => 'Core\src\Core\mvc\Service\ServiceListenerFactory',
            //'ModuleManager' => 'Core\src\Core\Service\ModuleManagerFactory',
            'Log' => 'Core\src\Core\Service\Log'
            ),            
         'aliases' => array(
             'mappers' => 'MapperManager',
             'builders' => 'EntityBuilderManager',
             'repositories' => 'RepositoryManager',
         ),
    ),
);