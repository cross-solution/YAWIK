<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution <http://cross-solution.de>
 * @license MIT
 */

ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

date_default_timezone_set('Europe/Berlin');

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
Zend\Mvc\Application::init(require 'config/config.php')->run();
