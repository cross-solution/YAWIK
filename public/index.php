<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution <http://cross-solution.de>
 * @license MIT
 */

ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

date_default_timezone_set('Europe/Berlin');

if(!version_compare(PHP_VERSION, '5.6.0', 'ge')){
    echo sprintf('<p>Sorry, YAWIK requires at least PHP 5.6.0 to run, but this server currently provides PHP %s</p>',PHP_VERSION);
    echo '<p>Please ask your servers\' administrator to install the proper PHP version.</p>';
    exit;
}

if (php_sapi_name() == 'cli-server') {
    $parseUrl = parse_url(substr($_SERVER["REQUEST_URI"], 1));
    $route = isset($parseUrl['path']) ? $parseUrl['path']:null;
    if (is_file(__DIR__ . '/' . $route)) {
        if(substr($route, -4) == ".php"){
            require __DIR__ . '/' . $route;     // Include requested script files
            exit;
        }
        return false;           // Serve file as is
    } else {                    // Fallback to index.php
        $_GET["q"] = $route;    // Try to emulate the behaviour of a .htaccess here.
    }
}

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
    echo '<p>Could not initialize autoloading. This happens, if the dependencies are not installed yet.</p>';
    echo '<p>Please try to install the dependencies via: </p>';
    echo '<code>cd '. realpath('.') .'<br>./install.sh</code>';
    echo '<p>exit at ' . __FILE__ . ' in line ' . __LINE__ .'</p>';
    exit;
}

// Run the application!
Zend\Mvc\Application::init(require 'config/config.php')->run();
