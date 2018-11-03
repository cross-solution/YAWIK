<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Geo;

/**
 * Geocoder Module
 *
 * This module provides a LocationSelect form element.
 */
class Module
{
    /**
    * Loads module specific configuration.
    *
    * @return array
    */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
