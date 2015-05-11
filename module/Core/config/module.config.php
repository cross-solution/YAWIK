<?php
/**
 * YAWIK
 * Configuration file of the Core module
 * 
 * This file intents to provide the configuration for all other modules
 * as well (convention over configuration).
 * Having said that, you may always overwrite or extend the configuration
 * in your own modules configuration file(s) (or via the config autoloading).
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

$doctrineConfig = include __DIR__ . '/doctrine.config.php';


return array(

    'doctrine' => $doctrineConfig,
    
    'Core' => array(
        'settings' => array(
            'entity' => '\\Core\\Entity\\SettingsContainer',
            'navigation_label' => /* @translate */ 'general settings',
            'navigation_class' => 'yk-icon yk-icon-settings'
        ),
    ),


    // Logging
    'log' => array(
        'Core/Log' => array(
            'writers' => array(
                 array(
                     'name' => 'stream',
                    'priority' => 1000,
                    'options' => array(
                         'stream' => __DIR__ .'/../../../log/yawik.log',
                    ),
                ),
            ),
        ),
        'Log/Core/Mail' => array(
            'writers' => array(
                 array(
                     'name' => 'stream',
                    'priority' => 1000,
                    'options' => array(
                         'stream' => __DIR__ .'/../../../log/mails.log',
                    ),
                ),
            ),
        ),
        'ErrorLogger' => array(
            'service' => 'Core/ErrorLogger',
            'config'  => array(
                'stream' => __DIR__ . '/../../../log/error.log',
                'log_errors' => true,
                'log_exceptions' => true,
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
                    'error' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/error',
                            'defaults' => array(
                                'controller' => 'Core\Controller\Index',
                                'action' => 'error',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
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
                    'content' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '/content/(?<view>.*)$',
                            'defaults' => array(
                                'controller' => 'Core\Controller\Content',
                                'action' => 'index',
                            ),
                            'spec' => '/content/%view%'
                        ),
                        'may_terminate' => true,

                    )
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
            'guest' => array(
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
            'Core/DoctrineMongoODM/RepositoryEvents' => '\Core\Repository\DoctrineMongoODM\Event\RepositoryEventsSubscriber',
            'defaultListeners'           => 'Core\Listener\DefaultListener',
            'templateProvider'           => 'Core\Service\TemplateProvider',
            'templateProviderStrategy'   => 'Core\Form\Hydrator\Strategy\TemplateProviderStrategy',
            'Core/Listener/Notification' => 'Core\Listener\NotificationListener',
            'Notification/Event'         => 'Core\Listener\Events\NotificationEvent',
        ),
        'factories' => array(
            'Core/DocumentManager' => 'Core\Repository\DoctrineMongoODM\DocumentManagerFactory',
            'Core/RepositoryService' => 'Core\Repository\RepositoryServiceFactory',
            'Core/MailService' => '\Core\Mail\MailServiceFactory',
            'Core/html2pdf' => '\Core\Html2Pdf\PdfServiceFactory',
//            'mvctranslator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'Core/Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'Core/ErrorLogger' => 'Core\Log\ErrorLoggerFactory',
            'Core/JsonEntityHydrator' => 'Core\Entity\Hydrator\JsonEntityHydratorFactory',
            'Core/Options' => 'Core\Factory\ModuleOptionsFactory',
        ),
        'abstract_factories' => array(
            'Core\Log\LoggerAbstractFactory',
        ),
        'aliases' => array(
            'forms' => 'FormElementManager',
            'repositories' => 'Core/RepositoryService',
            'translator' => 'mvctranslator',
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
    // Defines the Core/Navigation. 
    'navigation' => array(
        'default' => array(
             'home' => array(
                 'label' => /*@translate*/ 'Home',
                 'route' => 'lang',
                 'visible' => false
             ),
        ),
    ),
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
        'invokables' => array(
            'Core\Controller\Index' => 'Core\Controller\IndexController',
            'Core\Controller\Content' => 'Core\Controller\ContentController',
            'Core\Controller\File'  => 'Core\Controller\FileController',
        ),
    ),
    // Configuration of the controller plugin service manager
    'controller_plugins' => array(
        'factories' => array(
            'config' => 'Core\Controller\Plugin\ConfigFactory',
            'Notification' => '\Core\Controller\Plugin\Service\NotificationFactory',
        ),
        'invokables' => array(
            'listquery' => 'Core\Controller\Plugin\ListQuery',
            'Core/FileSender' => 'Core\Controller\Plugin\FileSender',
            'mail' => 'Core\Controller\Plugin\Mail',
            'Core/Mailer' => 'Core\Controller\Plugin\Mailer',
            'Core/CreatePaginator' => 'Core\Controller\Plugin\CreatePaginator',
            'Core/ContentCollector' => 'Core\Controller\Plugin\ContentCollector',
            'Core/PaginationParams' => 'Core\Controller\Plugin\PaginationParams',
        ),
        'aliases' => array(
            'filesender' => 'Core/FileSender',
            'mailer'     => 'Core/Mailer',
            'paginator' => 'Core/CreatePaginator',
            'paginationparams' => 'Core/PaginationParams',
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
            'pagination-control' => __DIR__ . '/../view/partial/pagination-control.phtml',
            'core/loading-popup' => __DIR__ . '/../view/partial/loading-popup.phtml',
            'core/notifications' => __DIR__ . '/../view/partial/notifications.phtml',
            'form/core/buttons' => __DIR__ . '/../view/form/buttons.phtml',
            'form/core/privacy' => __DIR__ . '/../view/form/privacy.phtml',
            'core/form/permissions-fieldset' => __DIR__ . '/../view/form/permissions-fieldset.phtml',
            'core/form/permissions-collection' => __DIR__ . '/../view/form/permissions-collection.phtml',
            'core/form/container-view' => __DIR__ . '/../view/form/container.view.phtml',
            'mail/footer' =>  __DIR__ . '/../view/mail/footer.phtml',
            //'startpage' => __DIR__ . '/../view/layout/startpage.phtml',
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
            'form_element' => 'Core\Form\View\Helper\FormElement',
//            'form_partial' => 'Core\Form\View\Helper\FormPartial',
//            'form_collection' => 'Core\Form\View\Helper\FormCollection',
//            'form_row' => 'Core\Form\View\Helper\FormRow',
//            'form_multi_checkbox' => 'Core\Form\View\Helper\FormMultiCheckbox',
//            'form_radio' => 'Core\Form\View\Helper\FormRadio',
//            'form_daterange' => 'Core\Form\View\Helper\FormDateRange',
            'form' => 'Core\Form\View\Helper\Form',
            'formsimple' => 'Core\Form\View\Helper\FormSimple',
            'formContainer' => 'Core\Form\View\Helper\FormContainer',
            'summaryForm' => 'Core\Form\View\Helper\SummaryForm',
            'filterForm' => 'Core\Form\View\Helper\FilterForm',
            'formPartial' => '\Core\Form\View\Helper\FormPartial',
            'formcollection' => 'Core\Form\View\Helper\FormCollection',
            'formrow' => 'Core\Form\View\Helper\FormRow',
            'formrowsimple' => 'Core\Form\View\Helper\FormSimpleRow',
            'formrowcombined' => 'Core\Form\View\Helper\FormRowCombined',
            'formfileupload' => 'Core\Form\View\Helper\FormFileUpload',
            'formimageupload' => 'Core\Form\View\Helper\FormImageUpload',
            'formcheckbox' => 'Core\Form\View\Helper\FormCheckbox',
            'formInfoCheckbox' => 'Core\Form\View\Helper\FormInfoCheckbox',
            'formselect' => 'Core\Form\View\Helper\FormSelect',
            'dateFormat' => 'Core\View\Helper\DateFormat',
            'salutation' => 'Core\View\Helper\Salutation',
            'period' => 'Core\View\Helper\Period',
            'link'   => 'Core\View\Helper\Link',
            'rating' => 'Core\View\Helper\Rating',
            'base64' => 'Core\View\Helper\Base64',
            'insertFile' => 'Core\View\Helper\InsertFile',
            'alert' => 'Core\View\Helper\Alert',
            'spinnerButton' => 'Core\Form\View\Helper\Element\SpinnerButton',
            'togglebutton' => 'Core\Form\View\Helper\ToggleButton',
        ),
        'factories' => array(
            'params' => 'Core\View\Helper\Service\ParamsHelperFactory',
            'configheadscript' => 'Core\View\Helper\Service\HeadScriptFactory',
        ),
        'initializers' => array(
//            '\Core\View\Helper\Service\HeadScriptInitializer',
        ),
    ),
    
    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_separator_string' => '</li><li>',
            'message_close_string'     => '</li></ul></div>',
        ),
    ),
    
    'filters' => array(
        'invokables' => array(
            'Core/Repository/PropertyToKeywords' => 'Core\Repository\Filter\PropertyToKeywords',
        ),
        'factories' => array(
            "Core/XssFilter" => "Core\Filter\XssFilterFactory",
       ),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'DefaultButtonsFieldset' => '\Core\Form\DefaultButtonsFieldset',
            'FormSubmitButtonsFieldset' => '\Core\Form\FormSubmitButtonsFieldset',
            'SummaryFormButtonsFieldset' => 'Core\Form\SummaryFormButtonsFieldset',
            'Checkbox' => 'Core\Form\Element\Checkbox',
            'InfoCheckbox' => 'Core\Form\Element\InfoCheckbox',
            'Core/ListFilterButtons' => '\Core\Form\ListFilterButtonsFieldset',
            'Core/FileUpload' => '\Core\Form\Element\FileUpload',
            'Core\FileCollection' => 'Core\Form\FileCollection',
            'Core/LocalizationSettingsFieldset' => 'Core\Form\LocalizationSettingsFieldset',
            'Core/RatingFieldset' => 'Core\Form\RatingFieldset',
            'Core/Rating' => 'Core\Form\Element\Rating',
            'Core/PermissionsFieldset' => 'Core\Form\PermissionsFieldset',
            'Core/PermissionsCollection' => 'Core\Form\PermissionsCollection',
            'Location' => 'Zend\Form\Element\Text',
            'Core/Spinner-Submit' => 'Core\Form\Element\SpinnerSubmit',
            'togglebutton' => 'Core\Form\Element\ToggleButton',
            'TextEditor' => 'Core\Form\Element\Editor',
            'TextEditorLight' => 'Core\Form\Element\EditorLight',

        ),
        'aliases' => array(
            'submitField' => 'FormSubmitButtonsFieldset'
        )
    ),
    
    'mails_config' => array(
        'from' => array(
            'email' => 'no-reply@host.tld',
            'name'  => 'YAWIK'
        ),
    ),
    
);
