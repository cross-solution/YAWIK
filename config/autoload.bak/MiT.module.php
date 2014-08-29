<?php
/**
 * Cross Applicant Management
 * Application configuration
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/**
 * returns an array with additional modules, which should be loaded
 * 
 * modules, that should not be loaded, start with a minus
 * this option depends on the order of the loaded modules
 */

if ((!array_key_exists('APPLICATION_HOST', $_SERVER) || $_SERVER['APPLICATION_HOST'] != "mediaintown") && !$allModules) {
    return array();
}

return array("CamMediaintown");