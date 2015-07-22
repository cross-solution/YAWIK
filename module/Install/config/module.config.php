<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

return array(


    'service_manager' => array(
        'invokables' => array(
            'Install/Listener/LanguageSetter' => 'Install\Listener\LanguageSetter',
        ),
    ),

    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Install/Index',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Install/Index' => 'Install\Controller\Index',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'Install/Prerequisites' => 'Install\Controller\Plugin\Prerequisites',
            'Install/ConfigCreator' => 'Install\Controller\Plugin\YawikConfigCreator',
        ),
    ),

    'form_elements' => array(
        'invokables' => array(
            'Install/Installation' => 'Install\Form\Installation',
        ),
    ),

    'validators' => array(
        'invokables' => array(
            'Install/ConnectionString' => 'Install\Validator\MongoDbConnectionString',
        ),
    ),

    // Configure the view service manager
    'view_manager' => array(
        'display_not_found_reason' => false,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/index',
        'unauthorized_template' => 'error/index',
        'exception_template' => 'error/index',
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => 'Install',
            ),
        ),
    ),
);

