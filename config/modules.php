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
    'Core',
    'Auth',
    'Cv',
    'Applications',
    'Jobs',
    'Orders',
    'Settings',
    'Pdf',
    'Geo',
    'Organizations',
    'ReleaseTools',
    'Yawik\\Migration'
];
