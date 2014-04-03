<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

return array('console' => array('router' => array('routes' => array(
    'jobs-genkeywords' => array(
        'options' => array(
            'route' => 'jobs generatekeywords [--filter=]',
            'defaults' => array(
                'controller' => 'Jobs/Console',
                'action' => 'generatekeywords',
            ),
        ),
    ),
    'jobs-setpermissions' => array(
        'options' => array(
            'route' => 'jobs setpermissions',
            'defaults' => array(
                'controller' => 'Jobs/Console',
                'action' => 'setpermissions',
            ),
        ),
    ),
))));
