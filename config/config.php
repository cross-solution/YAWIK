<?php

use Zend\Stdlib\ArrayUtils;

/**
 * YAWIK
 * Application configuration
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

$env = getenv('APPLICATION_ENV') ?: 'production';

$modules = array(
         'DoctrineModule', 'DoctrineMongoODMModule', 'Core', /*'TwbBundle', */'Auth', 'Cv', 'Applications', 'Jobs', 'Settings', 'Pdf', 'Geo',
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

$config = array(

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
            sprintf('config/autoload/{,*.}{global,%s,local}.php', $env)
        ),
        // Use the $env value to determine the state of the flag
        'config_cache_enabled' => ($env == 'production'),
        
        'config_cache_key' => $env,
        
        // Use the $env value to determine the state of the flag
        'module_map_cache_enabled' => ($env == 'production'),
        
        'module_map_cache_key' => 'module_map',
        
        'cache_dir' => 'cache/',
        
        // Use the $env value to determine the state of the flag
        'check_dependencies' => ($env != 'production'),
    ),
    
    'service_listener_options' => array(
        array(
            'service_manager' => 'SettingsManager',
            'config_key'      => 'settings',
            'interface'       => '\Core\ModuleManager\Feature\SettingsProviderInterface',
            'method'          => 'getUserSettings',
        ),
    ),
    
    'service_manager' => array(
        'invokables' => array(
            'SettingsManager' => 'Core\ModuleManager\SettingsManager',
            //'ServiceListenerInterface' => 'Core\mvc\Service\ServiceListener',
        ),
        'factories' => array(
            //'Log' => 'Core\src\Core\Service\Log'
        ),
        
    ),
);

$envConfigFile = __DIR__ . '/config.' . $env . '.php';
if (file_exists($envConfigFile)) {
    if (is_readable($envConfigFile)) {
        $envConfig = include $envConfigFile;
        $config = ArrayUtils::merge($config, $envConfig);
    } else {
        trigger_notice(
            sprintf('Environment config file "%s" is not readable.', $envConfigFile),
            E_USER_NOTICE
        );
    }
}

return $config;