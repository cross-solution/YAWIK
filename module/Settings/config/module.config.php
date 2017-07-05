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
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return [
	
	'doctrine' => [
		'driver' => [
			'odm_default' => [
				'drivers' => [
					'Settings\Entity' => 'annotation',
				],
			],
		],
		'eventmanager' => [
			'odm_default' => [
				'subscribers' => [
					'Settings/InjectEntityResolverListener',
				],
			],
		],
	],
	
	
	// Translations
	'translator' => [
		'translation_file_patterns' => [
			[
				'type' => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern' => '%s.mo',
			],
		],
	],
	// Routes
	'router' => [
		'routes' => [
			'lang' => [
				'child_routes' => [
					'settings' => [
						'type' => 'Segment',
						'options' => [
							'route' => '/settings[/:module]',
							'defaults' => [
								'controller' => 'Settings\Controller\Index',
								'action' => 'index',
								'module' => 'Core',
							],
						],
						'may_terminate' => true,
					],
				],
			],
		],
	],
	
	'acl' => ['rules' => [
		'user' => [
			'allow' => [
				'route/lang/settings',
				'Settings\Controller\Index',
			],
		],
	]],
	'navigation' => [
		'default' => [
			'settings' => [
				'label'    => /*@translate*/ 'Settings',
				'route'    => 'lang/settings',
				'resource' => 'route/lang/settings',
				'order'    => 100,
				'params'   => ['module' => null],
			],
		],
	],
	
	// Configuration of the controller service manager (Which loads controllers)
	'controllers' => [
		'factories' => [
			'Settings\Controller\Index' => [\Settings\Controller\IndexController::class,'factory']
		],
	],
	
	// Configure the view service manager
	'view_manager' => [
		// Map template to files. Speeds up the lookup through the template stack.
		'template_map' => [
		],
		
		// Where to look for view templates not mapped above
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
	
	'view_helpers' => [
		'invokables' => [
			'Settings/FormDisableElementsCapableFormSettings' => 'Settings\Form\View\Helper\FormDisableElementsCapableFormSettings',
		],
		'factories' => [
		],
	],
	
	'service_manager' => [
		'factories' => [
			'Settings' => '\Settings\Settings\SettingsFactory',
			'Settings/EntityResolver' => '\Settings\Repository\SettingsEntityResolverFactory',
			'Settings/InjectEntityResolverListener' => [\Settings\Repository\Event\InjectSettingsEntityResolverListener::class,'factory'],
		],
		'initializers' => [],
		'shared' => [],
		'aliases' => [],
	],
	
	'controller_plugins' => [
		'factories' => ['settings' => '\Settings\Controller\Plugin\SettingsFactory'],
	],
	
	'form_elements' => [
		'factories' => [
			'Settings/Form' => [\Settings\Form\AbstractSettingsForm::class,'factory'],
			'Settings/DisableElementsCapableFormSettingsFieldset' => [\Settings\Form\DisableElementsCapableFormSettingsFieldset::class,'factory'],
			'Settings/Fieldset' => \Settings\Form\Factory\SettingsFieldsetFactory::class,
		],
		'aliases' => [
		],
	],
	
	'filters' => [
		'invokables' => [
			'Settings/Filter/DisableElementsCapableFormSettings' => \Settings\Form\Filter\DisableElementsCapableFormSettings::class,
		]
	]


];
