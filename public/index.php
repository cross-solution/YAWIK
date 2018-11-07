<?php

/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution <http://cross-solution.de>
 * @license MIT
 */

umask(0000);
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

use Core\Application;

chdir(dirname(__DIR__));
$config = __DIR__.'/../config/config.php';
Application::init(include $config)->run();
