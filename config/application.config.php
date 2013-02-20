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
        'Core',
    ),
    
    // Where to search modules
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        )
    ),
    
    // What configuration files should be autoloaded 
    'config_glob_paths' => array(
        'config/autoload/{,*.}{global,local}.php'
    )
);