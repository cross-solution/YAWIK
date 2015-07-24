<?php
/**
 * YAWIK
 *
 * @copyright 2015 Cross Solution <http://cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
$env = getenv('APPLICATION_ENV') ?: 'production';
$map = array(
    //'Core\Listener\AjaxRenderListener' => __DIR__ . '/../Core/src/Core/Listener/AjaxRenderListener.php',
);

if ('production' == $env || 'test' == $env) {
    $map += include __DIR__ . '/src/autoload_classmap.php';
}

if ('test' == $env) {
    $map += include __DIR__ . '/test/autoload_classmap.php';
}

return $map;

