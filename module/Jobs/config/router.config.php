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
                            'controller' => 'Jobs\Controller\Index',
                            'action'     => 'index',
                        ),
                    ),
                    'may_terminate' => true,
                ),
                'save' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/saveJob',
                        'defaults' => array(
                            'controller' => 'Jobs\Controller\Manage',
                            'action' => 'save',
                        ),
                    ),
                    'may_terminate' => true,
                ),
            ),
        ),
    ),
);
