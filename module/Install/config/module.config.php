<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

return [
	'tracy' => [
		'enabled' => true, // flag whether to load tracy at all
		'mode' => true, // true = production|false = development|null = autodetect|IP address(es) csv/array
		'bar' => false, // bool = enabled|Toggle nette diagnostics bar.
		'strict' => true, // bool = cause immediate death|int = matched against error severity
		'log' => __DIR__.'/../../../var/log/tracy', // path to log directory (this directory keeps error.log, snoozing mailsent file & html exception trace files)
		'email' => null, // in production mode notifies the recipient
		'email_snooze' => 900 // interval for sending email in seconds
	],

    'doctrine' => [
        'driver' => [
            'annotation' => [
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
            ],
            'odm_default' => [
                'drivers' => [
                    'Auth\Entity' => 'annotation'
                ],
            ],
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Install/Listener/LanguageSetter' => 'Install\Listener\LanguageSetter',
        ],
	    'factories' => [
		    'mvctranslator' => \Zend\Mvc\I18n\TranslatorFactory::class,
		    'FilterManager' => \Zend\Filter\FilterPluginManagerFactory::class,
            'Tracy' => [\Core\Service\Tracy::class,'factory'],
            'Core/Options' => 'Core\Factory\ModuleOptionsFactory',
        ],
        'abstract_factories' => [
            'Core\Factory\OptionsAbstractFactory',
        ],
    ],

    'router' => [
        'routes' => [
            'index' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => \Install\Controller\Index::class,
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'controllers' => [
	    'abstract_factories' => [
	    	\Install\Factory\Controller\LazyControllerFactory::class
	    ],
    ],

    'controller_plugins' => [
        'invokables' => [
            'Install/Prerequisites' => 'Install\Controller\Plugin\Prerequisites',
        ],
        'factories' => [
            'Install/UserCreator'   => 'Install\Factory\Controller\Plugin\UserCreatorFactory',
            'Install/ConfigCreator' => 'Install\Factory\Controller\Plugin\YawikConfigCreatorFactory',
        ],
    ],

    'form_elements' => [
        'invokables' => [
            'Install/Installation' => 'Install\Form\Installation',
        ],
    ],

    'filters' => [
        'invokables' => [
            'Install/DbNameExtractor' => 'Install\Filter\DbNameExtractor',
            'Auth/CredentialFilter'    => 'Auth\Entity\Filter\CredentialFilter',
        ],
    ],

    'validators' => [
        'invokables' => [
            'InstallConnectionString' => \Install\Validator\MongoDbConnectionString::class,
            'InstallConnection'       => \Install\Validator\MongoDbConnection::class,
        ]
    ],

    // Configure the view service manager
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/index',
        'unauthorized_template' => 'error/index',
        'exception_template' => 'error/index',
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        // Where to look for view templates not mapped above
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            'configHeadScript' => 'Core\View\Helper\Service\HeadScriptFactory',
        ],
    ],

    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => 'Install',
            ],
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../../Core/language',
                'pattern' => 'Zend_Validate.%s.php',
                'text_domain' => 'default',
            ],
        ],
    ],
];
