<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

return array(
        'router' => array(
            'routes' => array(
                'applications' => array(
                    'options' => array(
                        'route' => 'applications generatekeywords [--filter=]',
                        'defaults' => array(
                            'controller' => 'Applications/Console',
                            'action' => 'generatekeywords',
                        ),
                    ),
                ),
            ),
        ),
);
