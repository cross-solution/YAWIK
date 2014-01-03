<?php

/**
 * Cross Applicant Management
 * Configuration file of the Core module
 * 
 * This file intents to provide the configuration for all other modules
 * as well (convention over configuration).
 * Having said that, you may always overwrite or extend the configuration
 * in your own modules configuration file(s) (or via the config autoloading).
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

return array(
    // Logging
    'log' => array(
        'writers' => array(
             array(
                 'name' => 'stream',
                 'priority' => 1000,
                 'options' => array(
                     'stream' => __DIR__ .'/../../../log/cam.log',
                ),
            ),
        ),
    ),
           
    // Routes
    'router' => array(
        'routes' => array(
            'lang' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:lang',
                    'defaults' => array(
                        'controller' => 'Core\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'mailtest' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/mail',
                            'defaults' => array(
                                'controller' => 'Core\Controller\Index',
                                'action' => 'mail',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
            'file' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/file/:filestore/:fileId[/:fileName]',
                    'defaults' => array(
                        'controller' => '\Core\Controller\File',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    
    'acl' => array(
        'rules' => array(
            'user' => array(
                'allow' => array(
                    //'route/file',
                    'Entity/File' => array(
                        '__ALL__' => 'Core/FileAccess'
                    ),
                ),
            ), 
        ),
        'assertions' => array(
            'invokables' => array(
                'Core/FileAccess' => 'Core\Acl\FileAccessAssertion',
            ),
        ),
    ),
    
    // Setup the service manager
    'service_manager' => array(
        'invokables' => array(
            'configaccess' => 'Core\Service\Config',
        ),
        'factories' => array(
            'Core/MailService' => '\Core\Mail\MailServiceFactory',
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'main_navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        'aliases' => array(
            'forms' => 'FormElementManager'
        ),
    ),
    // Translation settings consumed by the 'translator' factory above.
    'translator' => array(
        'locale' => 'de_DE',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => 'Zend_Validate.%s.php',
            )
        ),
    ),
    // Navigation-Konfig für die main_navigation
    'navigation' => array(
        'default' => array(
//             'home' => array(
//                 'label' => 'Home',
//                 'route' => 'lang',
//             ),
        ),
    ),
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
        'invokables' => array(
            'Core\Controller\Index' => 'Core\Controller\IndexController',
            'Core\Controller\File'  => 'Core\Controller\FileController',
        ),
    ),
    // Configuration of the controller plugin service manager
    'controller_plugins' => array(
        'factories' => array(
            'mailstackmailer' => 'Core\Controller\Plugin\Mailfactory',
            'config' => 'Core\Controller\Plugin\ConfigFactory',
        ),
        'invokables' => array(
            'listquery' => 'Core\Controller\Plugin\ListQuery',
            'Core/FileSender' => 'Core\Controller\Plugin\FileSender',
            'mail' => 'Core\Controller\Plugin\Mail',
            'Core/Mailer' => 'Core\Controller\Plugin\Mailer'
        ),
        'aliases' => array(
            'filesender' => 'Core/FileSender',
            'mailer'     => 'Core/Mailer',
        )
    ),
    // Configure the view service manager
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'unauthorized_template' => 'error/403',
        'exception_template' => 'error/index',
        // Map template to files. Speeds up the lookup through the template stack. 
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            //'core/index/index'        => __DIR__ . '/../view/core/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/403' => __DIR__ . '/../view/error/403.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'main-navigation' => __DIR__ . '/../view/partial/main-navigation.phtml',
            'core/loading-popup' => __DIR__ . '/../view/partial/loading-popup.phtml',
            'form/core/buttons' => __DIR__ . '/../view/form/buttons.phtml',
            'form/core/privacy' => __DIR__ . '/../view/form/privacy.phtml',
            
        ),
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'services' => 'Core\View\Helper\Services',
//            'form' => 'Core\Form\View\Helper\Form',
//            'form_element' => 'Core\Form\View\Helper\FormElement',
//            'form_partial' => 'Core\Form\View\Helper\FormPartial',
//            'form_collection' => 'Core\Form\View\Helper\FormCollection',
//            'form_row' => 'Core\Form\View\Helper\FormRow',
//            'form_multi_checkbox' => 'Core\Form\View\Helper\FormMultiCheckbox',
//            'form_radio' => 'Core\Form\View\Helper\FormRadio',
//            'form_daterange' => 'Core\Form\View\Helper\FormDateRange',
            'build_query' => 'Core\View\Helper\BuildQuery',
            'form' => 'Core\Form\View\Helper\Form',
            'formPartial' => '\Core\Form\View\Helper\FormPartial',
            'formcollection' => 'Core\Form\View\Helper\FormCollection',
            'formrow' => 'Core\Form\View\Helper\FormRow',
            'formrowcombined' => 'Core\Form\View\Helper\FormRowCombined',
            'dateFormat' => 'Core\View\Helper\DateFormat',
            'salutation' => 'Core\View\Helper\Salutation',
            'period' => 'Core\View\Helper\Period',
            'link'   => 'Core\View\Helper\Link',
            
        ),
        'factories' => array(
            'params' => 'Core\View\Helper\Service\ParamsHelperFactory',
        ),
    ),
    'form_elements' => array(
        'invokables' => array(
            'DefaultButtonsFieldset' => '\Core\Form\DefaultButtonsFieldset',
            'Core/ListFilterButtons' => '\Core\Form\ListFilterButtonsFieldset',
            'Core\FileCollection' => 'Core\Form\FileCollection',
        ),
    ),
    
    'mails' => array(
        'from' => array(
            'email' => 'no-reply@host.tld',
            'name'  => 'CrossApplicantManagement'
        ),
    ),
    
    'repositories' => array(
        
        'abstract_factories' => array(
            'Core\Repository\FileRepositoryAbstractFactory',
        )
    ),
    
    'mappers' => array(
        'abstract_factories' => array(
            'Core\Repository\Mapper\FileMapperAbstractFactory',
         ),
    ),
    
    'entity_builders' => array(
        'factories' => array(
            'Core/File' => 'Core\Repository\EntityBuilder\FileBuilderFactory',
            'Core/JsonFile' => 'Core\Repository\EntityBuilder\JsonFileBuilderFactory',
        ),
    ),
);
