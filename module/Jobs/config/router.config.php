<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** router.config.php */ 

// Routes
return array(
   
    'routes' => array(
        'lang' => array(
            'child_routes' => array(
                'jobs' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'    => '/jobs',
                        'defaults' => array(
                            'controller' => 'Jobs/Index',
                            'action'     => 'index',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'manage' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' => '/:action',
                                'defaults' => array(
                                    'controller' => 'Jobs/Manage',
                                ),
                            ),
                            'may_terminate' => true,
                        ),
                        'view'   => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => '/view',
                                'defaults' => array(
                                    'action' => 'view'
                                ),
                            ),
                            'may_terminate' => true,
                        ),
                    ),
                ),
                'save' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/saveJob',
                        'defaults' => array(
                            'controller' => 'Jobs/Import',
                            'action' => 'save',
                        ),
                    ),
                    'may_terminate' => true,
                ),
            ),
        ),
    ),
);
