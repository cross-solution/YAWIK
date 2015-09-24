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

if (php_sapi_name() == 'cli-server') {
    $route = parse_url(substr($_SERVER["REQUEST_URI"], 1))["path"];
    if (is_file($route)) {
        if(substr($route, -4) == ".php"){
            require $route;     // Include requested script files
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
