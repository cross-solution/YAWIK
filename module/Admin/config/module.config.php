<?php
/**
 * YAWIK
 * Configuration file of the Auth module
 * 
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(
    /*
     * Acl definitions.
     * Format
     * array($ROLE[:$PARENT] => $RESOURCES);
     * 
     * $ROLE: Role name
     * $PARENT: Coma separated list of roles to inherit from.
     * $RESOURCES: array of resources
     *      a resource is 
     *      1. a string: taken as resource name
     *                   (when prefixed with "!", a deny rule is created.)
     *      1.1 the "null" value: allow on all resources.
     *      2. a key => string pair:
     *          key is the resource name (optionally prefixed with "!")
     *          if key is "__ALL__" rule apply to all resources.
     *          string is the privilege name
     *      3. a key => array pair:
     *              key is the resource name (optionally prefixed with "!")
     *              array are the privileges which each of is
     *              1. a string: Taken as privilege name
     *              2. a key => string pair:
     *                  key is the privilege name
     *                  string is the name of the assertion class to instantiate and use with this rule.
     *              3. a key => array pair:
     *                  key is the privilege name
     *                  array is:
     *                      index 0: Name of the assertion class,
     *                      index 1: array of parameters to pass to the constructor of the assertion.
     *                  
     */
    'acl' => array(
        'roles' => array(
            'admin' => 'recruiter'
        ),
        
        'public_roles' => array(
            /*@translate*/ 'admin',
        ),
        'rules' => array(
            'admin' => array(
                'allow' => array(
                    'route/lang/admin',
                    'route/lang/my',
                    '/logout',
                    'route/lang/jobs/edit'
                ),
                'deny' => array(
                    'route/lang/auth',
                    'route/auth-provider',
                    'route/auth-hauth',
                    'route/auth-extern',
                ),
            ),
            'user' => array(
                'deny' => array(
                    'route/lang/admin',
                ),
            ),
            'recruiter' => array(
                'deny' => array(
                    'route/lang/admin'
                ),
            ),
        ),
    ),
    // Adds the Admin Link to the navigation
    'navigation' => array(
        'default' => array(
            'admin' => array(
                'label' => 'Admin',
                'route' => 'lang/admin',
                'order' => 1000,
                'resource' => 'route/lang/admin',
                'visible' => false,
                'pages' => array(
                    'list' => array(
                        'label' => /*@translate*/ 'Global Settings',
                        'route' => 'lang/admin',
                           'params' => array(
                                'section' => 'global-settings'
                            ),
                    ),
                    'hybridauth' => array(
                        'label' => /*@translate*/ 'Social Networks',
                        'route' => 'lang/admin',
                        'params' => array(
                            'section' => 'hybrid-auth'
                        ),
                    ),
                    'modules' => array(
                        'label' => /*@translate*/ 'Modules',
                        'route' => 'lang/admin',
                        'params' => array(
                            'section' => 'modules'
                        ),
                    ),
                    'static' =>array(
                        'label' => /*@translate*/ 'Static Pages',
                        'route' => 'lang/admin',
                        'params' => array(
                            'section' => 'static-pages'
                        ),
                    ),
                    'email' =>array(
                        'label' => /*@translate*/ 'Mail Settings',
                        'route' => 'lang/admin',
                        'params' => array(
                            'section' => 'mail-settings'
                        ),
                    ),
                ),
            ),
        ),
    ),

    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'router' => array('routes' =>
                        array('lang' =>
                                  array('child_routes' =>
                                            array(
                                                'admin' => array(
                                                    'type' => 'Segment',
                                                    'options' => array(
                                                        'route' => '/admin[/:section]',
                                                        'defaults' => array(
                                                            'controller' => 'Admin\Config',
                                                            'action' => 'index',
                                                            'section' => 'global-settings'
                                                        ),
                                                    ),
                                                    'may_terminate' => true,
                                                ),
                                            ),
                                  ),
                        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Admin/Config' => 'Admin\Controller\ConfigController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view',
        ),
    ),
);
