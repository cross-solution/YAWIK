<?php

/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution <http://cross-solution.de>
 * @license MIT
 */

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

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__.'/../.env');

chdir(dirname(__DIR__));
$config = include __DIR__.'/../config/config.php';
Core\Bootstrap::runApplication($config);
