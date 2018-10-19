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

if (!version_compare(PHP_VERSION, '5.6.0', 'ge')) {
    echo sprintf('<p>Sorry, YAWIK requires at least PHP 5.6.0 to run, but this server currently provides PHP %s</p>', PHP_VERSION);
    echo '<p>Please ask your servers\' administrator to install the proper PHP version.</p>';
    exit;
}


// Setup autoloading
// Composer autoloading
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    $loader = include __DIR__.'/../vendor/autoload.php';
    // Fallback
    //$loader->set($namespace, $path);
    $loader->set(null, array('module'));
} else {
    echo '<p>Could not initialize autoloading. This happens, if the dependencies are not installed yet.</p>';
    echo '<p>Please try to install the dependencies via: </p>';
    echo '<code>cd '. realpath('.') .'<br>./install.sh</code>';
    echo '<p>exit at ' . __FILE__ . ' in line ' . __LINE__ .'</p>';
    exit;
}

chdir(dirname(__DIR__));
$config = include __DIR__.'/../config/config.php';
Core\Bootstrap::runApplication($config);
