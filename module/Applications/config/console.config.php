<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
    /*
     * the application form creates temporary applications, as soon as a user opens the form.
     * This cli actions will cleanup unfinished applications.
     */
    'applications-cleanup' => array(
        'options' => array(
            'route' => 'applications cleanup [--limit=]',
            'defaults' => array(
                'controller' => 'Applications/Console',
                'action'     => 'cleanup'
            ),
        ),
    ),
    /*
     * list available view scripts
     */
    'applications-partials' => array(
        'options' => array(
            'route' => 'applications list',
            'defaults' => array(
                'controller' => 'Applications/Console',
                'action'     => 'listviewscripts'
            ),
        ),
    ),
))));
