<?php
/**
 * YAWIK
 *
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
$env = getenv('APPLICATION_ENV') ?: 'production';

return 'production' == $env
       ? include __DIR__ . '/src/autoload_classmap.php'
       : array(); /* no class map in test and development. */
