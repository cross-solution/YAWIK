<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution <http://cross-solution.de>
 * @license GPLv3
 */
 
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

date_default_timezone_set('Europe/Berlin');
//date_default_timezone_set('America/New_York');
/*
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
    // Fallback
    //$loader->set($namespace, $path);
    $loader->set(Null, array('module'));
} else {
    throw new \RuntimeException('Could not initialize autoloading.');
}
    
// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
