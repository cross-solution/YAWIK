<?php

/**
 * YAWIK
 * Module Configuration
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

if (getenv('ADDITIONAL_MODULES')) {

}

return [
    'SlmQueue',
    'Core',
    'Auth',
    'Cv',
    'Applications',
    'Jobs',
    'Settings',
    'Pdf',
    'Geo',
    'Organizations',
    'ReleaseTools',
    'Yawik\\Migration'
];
