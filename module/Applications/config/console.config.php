<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array('console' => array('router' => array('routes' => array(
    'applications-keywords' => array(
        'options' => array(
            'route' => 'applications generatekeywords [--filter=]',
            'defaults' => array(
                'controller' => 'Applications/Console',
                'action' => 'generatekeywords',
            ),
        ),
    ),
    'applications-rating' => array(
        'options' => array(
            'route' => 'applications calculate-rating [--filter=]',
            'defaults' => array(
                'controller' => 'Applications/Console',
                'action'     => 'calculateRating'
            ),
        ),
    ),
))));
