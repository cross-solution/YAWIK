<?php
return [
    'service_manager' =>
        [
            'aliases' =>
                [
                    'Di' => 'DependencyInjector',
                    'Laminas\\Di\\LocatorInterface' => 'DependencyInjector',
                    'Laminas\\Session\\SessionManager' => 'Laminas\\Session\\ManagerInterface',
                    'HttpRouter' => 'Laminas\\Router\\Http\\TreeRouteStack',
                    'router' => 'Laminas\\Router\\RouteStackInterface',
                    'Router' => 'Laminas\\Router\\RouteStackInterface',
                    'RoutePluginManager' => 'Laminas\\Router\\RoutePluginManager',
                    'navigation' => 'Laminas\\Navigation\\Navigation',
                    'TranslatorPluginManager' => 'Laminas\\I18n\\Translator\\LoaderPluginManager',
                    'InputFilterManager' => 'Laminas\\InputFilter\\InputFilterPluginManager',
                    'Laminas\\Form\\Annotation\\FormAnnotationBuilder' => 'FormAnnotationBuilder',
                    'Laminas\\Form\\Annotation\\AnnotationBuilder' => 'FormAnnotationBuilder',
                    'Laminas\\Form\\FormElementManager' => 'FormElementManager',
                    'ValidatorManager' => 'Laminas\\Validator\\ValidatorPluginManager',
                    'MvcTranslator' => 'Laminas\\Mvc\\I18n\\Translator',
                    'console' => 'ConsoleAdapter',
                    'Console' => 'ConsoleAdapter',
                    'ConsoleDefaultRenderingStrategy' => 'Laminas\\Mvc\\Console\\View\\DefaultRenderingStrategy',
                    'ConsoleRenderer' => 'Laminas\\Mvc\\Console\\View\\Renderer',
                    'HydratorManager' => 'Laminas\\Hydrator\\HydratorPluginManager',
                    'forms' => 'FormElementManager',
                    'repositories' => 'Core/RepositoryService',
                    'translator' => 'mvctranslator',
                    'assertions' => 'Acl\\AssertionManager',
                    'Auth/UserTokenGenerator' => 'Auth\\Service\\UserUniqueTokenGenerator',
                    'acl' => 'Acl',
                    'Applications/Listener/ApplicationStatusChangePost' => 'Applications/Listener/ApplicationStatusChangePre',
                ],
            'factories' =>
                [
                    'DependencyInjector' => 'Laminas\\ServiceManager\\Di\\DiFactory',
                    'DiAbstractServiceFactory' => 'Laminas\\ServiceManager\\Di\\DiAbstractServiceFactoryFactory',
                    'DiServiceInitializer' => 'Laminas\\ServiceManager\\Di\\DiServiceInitializerFactory',
                    'DiStrictAbstractServiceFactory' => 'Laminas\\ServiceManager\\Di\\DiStrictAbstractServiceFactoryFactory',
                    'Laminas\\Session\\Config\\ConfigInterface' => 'Laminas\\Session\\Service\\SessionConfigFactory',
                    'Laminas\\Session\\ManagerInterface' => 'Laminas\\Session\\Service\\SessionManagerFactory',
                    'Laminas\\Session\\Storage\\StorageInterface' => 'Laminas\\Session\\Service\\StorageFactory',
                    'Laminas\\Router\\Http\\TreeRouteStack' => 'Laminas\\Router\\Http\\HttpRouterFactory',
                    'Laminas\\Router\\RoutePluginManager' => 'Laminas\\Router\\RoutePluginManagerFactory',
                    'Laminas\\Router\\RouteStackInterface' => 'Laminas\\Router\\RouterFactory',
                    'Laminas\\Navigation\\Navigation' => 'Laminas\\Navigation\\Service\\DefaultNavigationFactory',
                    'Laminas\\I18n\\Translator\\TranslatorInterface' => 'Laminas\\I18n\\Translator\\TranslatorServiceFactory',
                    'Laminas\\I18n\\Translator\\LoaderPluginManager' => 'Laminas\\I18n\\Translator\\LoaderPluginManagerFactory',
                    'FilterManager' => 'Laminas\\Filter\\FilterPluginManagerFactory',
                    'Laminas\\InputFilter\\InputFilterPluginManager' => 'Laminas\\InputFilter\\InputFilterPluginManagerFactory',
                    'FormAnnotationBuilder' => 'Laminas\\Form\\Annotation\\AnnotationBuilderFactory',
                    'FormElementManager' => 'Laminas\\Form\\FormElementManagerFactory',
                    'Laminas\\Validator\\ValidatorPluginManager' => 'Laminas\\Validator\\ValidatorPluginManagerFactory',
                    'Laminas\\Log\\Logger' => 'Laminas\\Log\\LoggerServiceFactory',
                    'LogFilterManager' => 'Laminas\\Log\\FilterPluginManagerFactory',
                    'LogFormatterManager' => 'Laminas\\Log\\FormatterPluginManagerFactory',
                    'LogProcessorManager' => 'Laminas\\Log\\ProcessorPluginManagerFactory',
                    'LogWriterManager' => 'Laminas\\Log\\WriterPluginManagerFactory',
                    'Laminas\\Mvc\\I18n\\Translator' => 'Laminas\\Mvc\\I18n\\TranslatorFactory',
                    'ConsoleAdapter' => 'Laminas\\Mvc\\Console\\Service\\ConsoleAdapterFactory',
                    'ConsoleExceptionStrategy' => 'Laminas\\Mvc\\Console\\Service\\ConsoleExceptionStrategyFactory',
                    'ConsoleRouteNotFoundStrategy' => 'Laminas\\Mvc\\Console\\Service\\ConsoleRouteNotFoundStrategyFactory',
                    'ConsoleRouter' => 'Laminas\\Mvc\\Console\\Router\\ConsoleRouterFactory',
                    'ConsoleViewManager' => 'Laminas\\Mvc\\Console\\Service\\ConsoleViewManagerFactory',
                    'Laminas\\Mvc\\Console\\View\\DefaultRenderingStrategy' => 'Laminas\\Mvc\\Console\\Service\\DefaultRenderingStrategyFactory',
                    'Laminas\\Mvc\\Console\\View\\Renderer' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Hydrator\\HydratorPluginManager' => 'Laminas\\Hydrator\\HydratorPluginManagerFactory',
                    'SerializerAdapterManager' => 'Laminas\\Serializer\\AdapterPluginManagerFactory',
                    'doctrine.cli' => 'DoctrineModule\\Service\\CliFactory',
                    'configaccess' => 'Core\\Service\\Config::factory',
                    'templateProvider' => 'Core\\Service\\TemplateProvider::factory',
                    'Core/DocumentManager' => 'Core\\Repository\\DoctrineMongoODM\\DocumentManagerFactory',
                    'Core/RepositoryService' => 'Core\\Repository\\RepositoryServiceFactory',
                    'Core/MailService' => '\\Core\\Mail\\MailServiceFactory',
                    'Core/PaginatorService' => '\\Core\\Paginator\\PaginatorServiceFactory',
                    'Core/Html2Pdf' => '\\Core\\Html2Pdf\\PdfServiceFactory',
                    'Core/Navigation' => 'Core\\Factory\\Navigation\\DefaultNavigationFactory',
                    'modules/Core/jsonEntityHydrator' => 'Core\\Entity\\Hydrator\\JsonEntityHydratorFactory',
                    'Core/EntityHydrator' => 'Core\\Entity\\Hydrator\\EntityHydratorFactory',
                    'Core/Options' => 'Core\\Factory\\ModuleOptionsFactory',
                    'Core/DoctrineMongoODM/RepositoryEvents' =>
                        [
                            0 => 'Core\\Repository\\DoctrineMongoODM\\Event\\RepositoryEventsSubscriber',
                            1 => 'factory',
                        ],
                    'DefaultListeners' =>
                        [
                            0 => 'Core\\Listener\\DefaultListener',
                            1 => 'factory',
                        ],
                    'templateProviderStrategy' =>
                        [
                            0 => 'Core\\Form\\Hydrator\\Strategy\\TemplateProviderStrategy',
                            1 => 'factory',
                        ],
                    'Core/Listener/DeferredListenerAggregate' =>
                        [
                            0 => 'Core\\Listener\\DeferredListenerAggregate',
                            1 => 'factory',
                        ],
                    'Core/Listener/CreatePaginator' => 'Core\\Listener\\CreatePaginatorListener::factory',
                    'Core/Locale' => 'Core\\I18n\\LocaleFactory',
                    'mvctranslator' => 'Laminas\\Mvc\\I18n\\TranslatorFactory',
                    'Core\\Listener\\AjaxRouteListener' => 'Core\\Factory\\Listener\\AjaxRouteListenerFactory',
                    'Core\\Listener\\DeleteImageSetListener' => 'Core\\Factory\\Listener\\DeleteImageSetListenerFactory',
                    'Imagine' => 'Core\\Factory\\Service\\ImagineFactory',
                    'Core/Listener/Notification' =>
                        [
                            0 => 'Core\\Listener\\NotificationListener',
                            1 => 'factory',
                        ],
                    'HybridAuth' => '\\Auth\\Factory\\Service\\HybridAuthFactory',
                    'HybridAuthAdapter' => '\\Auth\\Factory\\Adapter\\HybridAuthAdapterFactory',
                    'ExternalApplicationAdapter' => '\\Auth\\Factory\\Adapter\\ExternalApplicationAdapterFactory',
                    'Auth/Adapter/UserLogin' => '\\Auth\\Factory\\Adapter\\UserAdapterFactory',
                    'AuthenticationService' => '\\Auth\\Factory\\Service\\AuthenticationServiceFactory',
                    'UnauthorizedAccessListener' => '\\Auth\\Factory\\Listener\\ExceptionStrategyFactory',
                    'DeactivatedUserListener' => '\\Auth\\Factory\\Listener\\ExceptionStrategyFactory',
                    'Auth\\Listener\\MailForgotPassword' => '\\Auth\\Factory\\Listener\\MailForgotPasswordFactory',
                    'Auth\\Listener\\SendRegistrationNotifications' => 'Auth\\Factory\\Listener\\SendRegistrationNotificationsFactory',
                    'Auth/CheckPermissionsListener' => 'Acl\\Listener\\CheckPermissionsListenerFactory',
                    'Acl' => 'Acl\\Factory\\Service\\AclFactory',
                    'Acl\\AssertionManager' => 'Acl\\Assertion\\AssertionManagerFactory',
                    'Auth\\Form\\ForgotPassword' => 'Auth\\Factory\\Form\\ForgotPasswordFactory',
                    'Auth\\Service\\ForgotPassword' => 'Auth\\Factory\\Service\\ForgotPasswordFactory',
                    'Auth\\Service\\UserUniqueTokenGenerator' => 'Auth\\Factory\\Service\\UserUniqueTokenGeneratorFactory',
                    'Auth\\Service\\GotoResetPassword' => 'Auth\\Factory\\Service\\GotoResetPasswordFactory',
                    'Auth\\Service\\Register' => 'Auth\\Factory\\Service\\RegisterFactory',
                    'Auth\\Service\\RegisterConfirmation' => 'Auth\\Factory\\Service\\RegisterConfirmationFactory',
                    'Auth/Dependency/Manager' => 'Auth\\Factory\\Dependency\\ManagerFactory',
                    'Applications/Options' => 'Applications\\Factory\\ModuleOptionsFactory',
                    'ApplicationRepository' => 'Applications\\Repository\\Service\\ApplicationRepositoryFactory',
                    'ApplicationMapper' => 'Applications\\Repository\\Service\\ApplicationMapperFactory',
                    'EducationMapper' => 'Applications\\Repository\\Service\\EducationMapperFactory',
                    'Applications/Listener/ApplicationCreated' => 'Applications\\Factory\\Listener\\EventApplicationCreatedFactory',
                    'Applications/Listener/ApplicationStatusChangePre' => 'Applications\\Factory\\Listener\\StatusChangeFactory',
                    'Applications\\Auth\\Dependency\\ListListener' => 'Applications\\Factory\\Auth\\Dependency\\ListListenerFactory',
                    'Applications\\Listener\\JobSelectValues' => 'Applications\\Factory\\Listener\\JobSelectValuesFactory',
                    'Jobs/Options' => 'Jobs\\Factory\\ModuleOptionsFactory',
                    'Jobs/Options/Provider' => 'Jobs\\Factory\\Options\\ProviderOptionsFactory',
                    'Jobs/Options/Channel' => 'Jobs\\Factory\\Options\\ChannelOptionsFactory',
                    'Jobs\\Form\\Hydrator\\OrganizationNameHydrator' => 'Jobs\\Factory\\Form\\Hydrator\\OrganizationNameHydratorFactory',
                    'modules/Jobs/jsonJobsEntityHydrator' => 'Jobs\\Entity\\Hydrator\\JsonJobsEntityHydratorFactory',
                    'Jobs/RestClient' => 'Jobs\\Factory\\Service\\JobsPublisherFactory',
                    'Jobs/Listener/MailSender' => 'Jobs\\Factory\\Listener\\MailSenderFactory',
                    'Jobs/Listener/AdminWidgetProvider' => 'Jobs\\Factory\\Listener\\AdminWidgetProviderFactory',
                    'Jobs/ViewModelTemplateFilter' => 'Jobs\\Factory\\Filter\\ViewModelTemplateFilterFactory',
                    'Jobs\\Model\\ApiJobDehydrator' => 'Jobs\\Factory\\Model\\ApiJobDehydratorFactory',
                    'Jobs/Listener/Publisher' =>
                        [
                            0 => 'Jobs\\Listener\\Publisher',
                            1 => 'factory',
                        ],
                    'Jobs/PreviewLinkHydrator' => 'Jobs\\Form\\Hydrator\\PreviewLinkHydrator::factory',
                    'Jobs\\Auth\\Dependency\\ListListener' => 'Jobs\\Factory\\Auth\\Dependency\\ListListenerFactory',
                    'Jobs/DefaultCategoriesBuilder' => 'Jobs\\Factory\\Repository\\DefaultCategoriesBuilderFactory',
                    'Jobs\\Listener\\DeleteJob' => 'Jobs\\Factory\\Listener\\DeleteJobFactory',
                    'Jobs\\Listener\\GetOrganizationManagers' => 'Jobs\\Factory\\Listener\\GetOrganizationManagersFactory',
                    'Jobs\\Listener\\LoadActiveOrganizations' => 'Jobs\\Factory\\Listener\\LoadActiveOrganizationsFactory',
                    'Settings' => '\\Settings\\Settings\\SettingsFactory',
                    'Settings/EntityResolver' => '\\Settings\\Repository\\SettingsEntityResolverFactory',
                    'Settings/InjectEntityResolverListener' =>
                        [
                            0 => 'Settings\\Repository\\Event\\InjectSettingsEntityResolverListener',
                            1 => 'factory',
                        ],
                    'Html2PdfConverter' =>
                        [
                            0 => 'Pdf\\Module',
                            1 => 'factory',
                        ],
                    'Geo/Client' => 'Geo\\Factory\\Service\\ClientFactory',
                    'Geo\\Listener\\AjaxQuery' => 'Geo\\Factory\\Listener\\AjaxQueryFactory',
                    'Organizations\\Auth\\Dependency\\ListListener' => 'Organizations\\Factory\\Auth\\Dependency\\ListListenerFactory',
                    'Organizations\\ImageFileCache\\Manager' => 'Organizations\\Factory\\ImageFileCache\\ManagerFactory',
                    'Organizations\\ImageFileCache\\ODMListener' => 'Organizations\\Factory\\ImageFileCache\\ODMListenerFactory',
                    'Organizations\\ImageFileCache\\ApplicationListener' => 'Organizations\\Factory\\ImageFileCache\\ApplicationListenerFactory',
                ],
            'abstract_factories' =>
                [
                    0 => 'Laminas\\Session\\Service\\ContainerAbstractServiceFactory',
                    1 => 'Laminas\\Navigation\\Service\\NavigationAbstractServiceFactory',
                    2 => 'Laminas\\InputFilter\\InputFilterAbstractServiceFactory',
                    3 => 'Laminas\\Form\\FormAbstractServiceFactory',
                    4 => 'Laminas\\Log\\LoggerAbstractServiceFactory',
                    'DoctrineModule' => 'DoctrineModule\\ServiceFactory\\AbstractDoctrineServiceFactory',
                    5 => 'Core\\Factory\\OptionsAbstractFactory',
                    6 => 'Core\\Factory\\EventManager\\EventManagerAbstractFactory',
                ],
            'delegators' =>
                [
                    'ViewHelperManager' =>
                        [
                            0 => 'Laminas\\Navigation\\View\\ViewHelperManagerDelegatorFactory',
                            1 => 'Laminas\\Mvc\\Console\\Service\\ConsoleViewHelperManagerDelegatorFactory',
                        ],
                    'HttpRouter' =>
                        [
                            0 => 'Laminas\\Mvc\\I18n\\Router\\HttpRouterDelegatorFactory',
                        ],
                    'Laminas\\Router\\Http\\TreeRouteStack' =>
                        [
                            0 => 'Laminas\\Mvc\\I18n\\Router\\HttpRouterDelegatorFactory',
                        ],
                    'ControllerManager' =>
                        [
                            0 => 'Laminas\\Mvc\\Console\\Service\\ControllerManagerDelegatorFactory',
                        ],
                    'Request' =>
                        [
                            0 => 'Laminas\\Mvc\\Console\\Service\\ConsoleRequestDelegatorFactory',
                        ],
                    'Response' =>
                        [
                            0 => 'Laminas\\Mvc\\Console\\Service\\ConsoleResponseDelegatorFactory',
                        ],
                    'Laminas\\Router\\RouteStackInterface' =>
                        [
                            0 => 'Laminas\\Mvc\\Console\\Router\\ConsoleRouterDelegatorFactory',
                        ],
                    'Laminas\\Mvc\\SendResponseListener' =>
                        [
                            0 => 'Laminas\\Mvc\\Console\\Service\\ConsoleResponseSenderDelegatorFactory',
                        ],
                    'ViewManager' =>
                        [
                            0 => 'Laminas\\Mvc\\Console\\Service\\ViewManagerDelegatorFactory',
                        ],
                ],
            'invokables' =>
                [
                    'DoctrineModule\\Authentication\\Storage\\Session' => 'Laminas\\Authentication\\Storage\\Session',
                    'Notification/Event' => 'Core\\Listener\\Events\\NotificationEvent',
                    'Core/EventManager' => 'Core\\EventManager\\EventManager',
                    'Core/Options/ImagineOptions' => 'Core\\Options\\ImagineOptions',
                    'SessionManager' => '\\Laminas\\Session\\SessionManager',
                    'Auth\\Form\\ForgotPasswordInputFilter' => 'Auth\\Form\\ForgotPasswordInputFilter',
                    'Auth\\Form\\RegisterInputFilter' => 'Auth\\Form\\RegisterInputFilter',
                    'Auth\\Form\\LoginInputFilter' => 'Auth\\Form\\LoginInputFilter',
                    'Auth\\LoginFilter' => 'Auth\\Filter\\LoginFilter',
                    'Applications/Options/ModuleOptions' => 'Applications\\Options\\ModuleOptions',
                    'Jobs/Event' => 'Jobs\\Listener\\Events\\JobEvent',
                    'Organizations\\Auth\\Dependency\\EmployeeListListener' => 'Organizations\\Auth\\Dependency\\EmployeeListListener',
                ],
            'shared' =>
                [
                    'Core/Listener/DeferredListenerAggregate' => false,
                    'Jobs/Event' => false,
                    'Jobs/Options/Channel' => false,
                ],
            'initializers' =>
                [
                ],
        ],
    'route_manager' =>
        [
            'factories' =>
                [
                    'symfony_cli' => 'DoctrineModule\\Service\\SymfonyCliRouteFactory',
                ],
        ],
    'router' =>
        [
            'routes' =>
                [
                    'lang' =>
                        [
                            'type' => 'Segment',
                            'options' =>
                                [
                                    'route' => '/:lang',
                                    'defaults' =>
                                        [
                                            'controller' => 'Core\\Controller\\Index',
                                            'action' => 'index',
                                        ],
                                ],
                            'may_terminate' => true,
                            'child_routes' =>
                                [
                                    'admin' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/admin',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Core/Admin',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                            'child_routes' =>
                                                [
                                                    'jobs' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/jobs[/:action]',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Admin',
                                                                            'action' => 'index',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'jobs-categories' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/jobs/categories',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/AdminCategories',
                                                                            'action' => 'index',
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                        ],
                                    'error' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/error',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Core\\Controller\\Index',
                                                            'action' => 'error',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'mailtest' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/mail',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Core\\Controller\\Index',
                                                            'action' => 'mail',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'content' =>
                                        [
                                            'type' => 'Regex',
                                            'options' =>
                                                [
                                                    'regex' => '/content/(?<view>.*]$',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Core\\Controller\\Content',
                                                            'action' => 'index',
                                                        ],
                                                    'spec' => '/content/%view%',
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'auth' =>
                                        [
                                            'type' => 'Laminas\\Router\\Http\\Literal',
                                            'options' =>
                                                [
                                                    'route' => '/login',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\Index',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'my' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/my/:action',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\Manage',
                                                            'action' => 'profile',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'my-password' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/my/password',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\Password',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'my-groups' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/my/groups[/:action]',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth/ManageGroups',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'forgot-password' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/auth/forgot-password',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\ForgotPassword',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'goto-reset-password' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/auth/goto-reset-password/:token/:userId',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\GotoResetPassword',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'register' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/auth/register[/:role]',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\Register',
                                                            'action' => 'index',
                                                            'role' => 'recruiter',
                                                        ],
                                                    'constraints' =>
                                                        [
                                                            'role' => '(recruiter|user]',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'register-confirmation' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/auth/register-confirmation/:userId',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\RegisterConfirmation',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'user-list' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/user/list',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth/Users',
                                                            'action' => 'list',
                                                        ],
                                                ],
                                        ],
                                    'user-edit' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/user/edit/:id',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth/Users',
                                                            'action' => 'edit',
                                                        ],
                                                    'constraints' =>
                                                        [
                                                            'id' => '\\w+',
                                                        ],
                                                ],
                                        ],
                                    'user-remove' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/user/remove',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth\\Controller\\Remove',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                        ],
                                    'user-switch' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/user/switch',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Auth/Users',
                                                            'action' => 'switch',
                                                        ],
                                                ],
                                        ],
                                    'cvs' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/cvs',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Cv/Index',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                            'child_routes' =>
                                                [
                                                    'create' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/create',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Cv\\Controller\\Manage',
                                                                            'action' => 'form',
                                                                        ],
                                                                ],
                                                        ],
                                                    'edit' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/edit/:id',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Cv\\Controller\\Manage',
                                                                            'action' => 'form',
                                                                        ],
                                                                ],
                                                        ],
                                                    'view' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/view/:id',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Cv/View',
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                        ],
                                    'my-cv' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/my/cv',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Cv\\Controller\\Manage',
                                                            'action' => 'form',
                                                            'id' => '__my__',
                                                        ],
                                                ],
                                        ],
                                    'apply' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/apply[/:channel]/:applyId',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Applications\\Controller\\Apply',
                                                            'action' => 'index',
                                                            'defaults' =>
                                                                [
                                                                    'channel' => 'default',
                                                                ],
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'apply-one-click' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/apply-one-click/:applyId/:network[/:immediately]',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Applications\\Controller\\Apply',
                                                            'action' => 'oneClickApply',
                                                        ],
                                                    'constraints' =>
                                                        [
                                                            'network' => 'facebook|xing|linkedin',
                                                            'immediately' => '0|1',
                                                        ],
                                                ],
                                        ],
                                    'applications-dashboard' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/applications-dashboard',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Applications\\Controller\\Index',
                                                            'action' => 'dashboard',
                                                        ],
                                                ],
                                        ],
                                    'applications' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/applications',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Applications/Controller/Manage',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                            'child_routes' =>
                                                [
                                                    'detail' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/:id',
                                                                    'constraints' =>
                                                                        [
                                                                            'id' => '[a-z0-9]+',
                                                                        ],
                                                                    'defaults' =>
                                                                        [
                                                                            'action' => 'detail',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                            'child_routes' =>
                                                                [
                                                                    'status' =>
                                                                        [
                                                                            'type' => 'Segment',
                                                                            'options' =>
                                                                                [
                                                                                    'route' => '/:status',
                                                                                    'defaults' =>
                                                                                        [
                                                                                            'action' => 'status',
                                                                                            'status' => 'bad',
                                                                                        ],
                                                                                    'constraints' =>
                                                                                        [
                                                                                            'status' => '[a-z]+',
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                        ],
                                                    'disclaimer' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/disclaimer',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Applications\\Controller\\Index',
                                                                            'action' => 'disclaimer',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'comments' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/comments/:action',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Applications/CommentController',
                                                                        ],
                                                                ],
                                                        ],
                                                    'applications-list' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/multi/:action',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Applications\\Controller\\MultiManage',
                                                                            'action' => 'multimodal',
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                        ],
                                    'api-jobs' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/api/jobs',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Jobs/ApiJobList',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                            'child_routes' =>
                                                [
                                                    'completion' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/organization/:organizationId',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/ApiJobListByOrganization',
                                                                            'action' => 'index',
                                                                            'defaults' =>
                                                                                [
                                                                                    'defaults' =>
                                                                                        [
                                                                                            'organizationId' => 0,
                                                                                        ],
                                                                                    'constraints' =>
                                                                                        [
                                                                                            'organizationId' => '[a-f0-9]+',
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                ],
                                        ],
                                    'jobs' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/jobs',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Jobs/Index',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                            'child_routes' =>
                                                [
                                                    'completion' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/completion/:id',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'completion',
                                                                            'defaults' =>
                                                                                [
                                                                                    'defaults' =>
                                                                                        [
                                                                                            'id' => 0,
                                                                                        ],
                                                                                    'constraints' =>
                                                                                        [
                                                                                            'id' => '[a-f0-9]+',
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'manage' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/:action',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'edit',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'check_apply_id' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/check-apply-id',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'check-apply-id',
                                                                            'forceJson' => true,
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'view' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/view[/:channel]',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Template',
                                                                            'action' => 'view',
                                                                            'defaults' =>
                                                                                [
                                                                                    'channel' => 'default',
                                                                                ],
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'history' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/history/:id',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'history',
                                                                            'defaults' =>
                                                                                [
                                                                                    'id' => 0,
                                                                                ],
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'dashboardjobs' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/dashboard',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Index',
                                                                            'action' => 'dashboard',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'editTemplate' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/editTemplate/:id',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Template',
                                                                            'action' => 'edittemplate',
                                                                            'defaults' =>
                                                                                [
                                                                                    'id' => 0,
                                                                                ],
                                                                            'constraints' =>
                                                                                [
                                                                                    'id' => '[a-f0-9]+',
                                                                                ],
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'template' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/template/:id/:template',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'template',
                                                                            'forceJson' => true,
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'approval' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/approval[/:state]',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'approval',
                                                                            'defaults' =>
                                                                                [
                                                                                    'state' => 'pending',
                                                                                ],
                                                                            'constraints' =>
                                                                                [
                                                                                    'state' => '(pending|approved|declined]',
                                                                                ],
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'deactivate' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/deactivate',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Manage',
                                                                            'action' => 'deactivate',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'assign-user' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/assign-user',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/AssignUser',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                    'listOpenJobs' =>
                                                        [
                                                            'type' => 'Literal',
                                                            'options' =>
                                                                [
                                                                    'route' => '/list-pending-jobs',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Jobs/Approval',
                                                                            'action' => 'listOpenJobs',
                                                                        ],
                                                                ],
                                                            'may_terminate' => true,
                                                        ],
                                                ],
                                        ],
                                    'save' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/saveJob',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Jobs/Import',
                                                            'action' => 'save',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'jobboard' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/jobboard',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Jobs/Jobboard',
                                                            'action' => 'index',
                                                        ],
                                                ],
                                        ],
                                    'landingPage' =>
                                        [
                                            'type' => 'Regex',
                                            'options' =>
                                                [
                                                    'regex' => '/jobs/(?<q>[a-zA-Z0-9_-]+]\\.html$',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Jobs/Jobboard',
                                                            'action' => 'index',
                                                        ],
                                                    'spec' => '/jobs/%q%.%format%',
                                                ],
                                        ],
                                    'export' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/export[/:format][/:channel]',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Jobs/ApiJobListByChannel',
                                                            'action' => 'list',
                                                            'defaults' =>
                                                                [
                                                                    'format' => 'xml',
                                                                    'channel' => 'default',
                                                                ],
                                                            'constraints' =>
                                                                [
                                                                    'format' => '(xml]',
                                                                ],
                                                        ],
                                                ],
                                        ],
                                    'settings' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/settings[/:module]',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Settings\\Controller\\Index',
                                                            'action' => 'index',
                                                            'module' => 'Core',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'geo' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/geo[/:plugin]',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Geo\\Controller\\Index',
                                                            'action' => 'index',
                                                            'module' => 'Geo',
                                                            'plugin' => 'photon',
                                                        ],
                                                    'constraints' =>
                                                        [
                                                            'plugin' => '(geo|photon]',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                        ],
                                    'organizations' =>
                                        [
                                            'type' => 'Segment',
                                            'options' =>
                                                [
                                                    'route' => '/organizations',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Organizations/Index',
                                                            'action' => 'index',
                                                            'module' => 'Organizations',
                                                        ],
                                                ],
                                            'may_terminate' => true,
                                            'child_routes' =>
                                                [
                                                    'detail' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/detail/:id',
                                                                    'constraints' =>
                                                                        [
                                                                            'id' => '\\w*',
                                                                        ],
                                                                    'defaults' =>
                                                                        [
                                                                            'action' => 'detail',
                                                                            'id' => '0',
                                                                        ],
                                                                ],
                                                        ],
                                                    'logo' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/logo/:id',
                                                                    'constraints' =>
                                                                        [
                                                                            'id' => '\\w+',
                                                                        ],
                                                                    'defaults' =>
                                                                        [
                                                                            'action' => 'logo',
                                                                        ],
                                                                ],
                                                        ],
                                                    'form' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/form',
                                                                    'defaults' =>
                                                                        [
                                                                            'action' => 'form',
                                                                        ],
                                                                ],
                                                        ],
                                                    'list' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/list',
                                                                    'defaults' =>
                                                                        [
                                                                            'action' => 'list',
                                                                        ],
                                                                ],
                                                        ],
                                                    'edit' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/edit[/:id]',
                                                                    'constraints' =>
                                                                        [
                                                                            'id' => '\\w+',
                                                                        ],
                                                                    'defaults' =>
                                                                        [
                                                                            'action' => 'edit',
                                                                        ],
                                                                ],
                                                        ],
                                                    'invite' =>
                                                        [
                                                            'type' => 'Segment',
                                                            'options' =>
                                                                [
                                                                    'route' => '/invite[/:action]',
                                                                    'defaults' =>
                                                                        [
                                                                            'controller' => 'Organizations/InviteEmployee',
                                                                            'action' => 'invite',
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                        ],
                                    'my-organization' =>
                                        [
                                            'type' => 'Literal',
                                            'options' =>
                                                [
                                                    'route' => '/my/organization',
                                                    'defaults' =>
                                                        [
                                                            'controller' => 'Organizations/Index',
                                                            'action' => 'edit',
                                                            'id' => '__my__',
                                                        ],
                                                ],
                                        ],
                                ],
                        ],
                    'file' =>
                        [
                            'type' => 'Segment',
                            'options' =>
                                [
                                    'route' => '/file/:filestore/:fileId[/:fileName]',
                                    'defaults' =>
                                        [
                                            'controller' => 'Core\\Controller\\File',
                                            'action' => 'index',
                                        ],
                                ],
                            'may_terminate' => true,
                        ],
                    'auth-provider' =>
                        [
                            'type' => 'Segment',
                            'options' =>
                                [
                                    'route' => '/login/:provider',
                                    'constraints' =>
                                        [
                                        ],
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth\\Controller\\Index',
                                            'action' => 'login',
                                        ],
                                ],
                        ],
                    'auth-hauth' =>
                        [
                            'type' => 'Literal',
                            'options' =>
                                [
                                    'route' => '/login/hauth',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth\\Controller\\HybridAuth',
                                            'action' => 'index',
                                        ],
                                ],
                        ],
                    'auth-extern' =>
                        [
                            'type' => 'Literal',
                            'options' =>
                                [
                                    'route' => '/login/extern',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth\\Controller\\Index',
                                            'action' => 'login-extern',
                                            'forceJson' => true,
                                        ],
                                ],
                            'may_terminate' => true,
                        ],
                    'auth-social-profiles' =>
                        [
                            'type' => 'Literal',
                            'options' =>
                                [
                                    'route' => '/auth/social-profiles',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth/SocialProfiles',
                                            'action' => 'fetch',
                                        ],
                                ],
                        ],
                    'auth-group' =>
                        [
                            'type' => 'Literal',
                            'options' =>
                                [
                                    'route' => '/auth/groups',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth\\Controller\\Index',
                                            'action' => 'group',
                                            'forceJson' => true,
                                        ],
                                ],
                            'may_terminate' => true,
                        ],
                    'auth-logout' =>
                        [
                            'type' => 'Literal',
                            'options' =>
                                [
                                    'route' => '/logout',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth\\Controller\\Index',
                                            'action' => 'logout',
                                        ],
                                ],
                        ],
                    'user-image' =>
                        [
                            'type' => 'Segment',
                            'options' =>
                                [
                                    'route' => '/user/image/:id',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth\\Controller\\Image',
                                            'action' => 'index',
                                            'id' => 0,
                                        ],
                                ],
                        ],
                    'user-search' =>
                        [
                            'type' => 'Literal',
                            'options' =>
                                [
                                    'route' => '/user/search',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth/ManageGroups',
                                            'action' => 'search-users',
                                        ],
                                ],
                        ],
                    'test-hybrid' =>
                        [
                            'type' => 'Segment',
                            'options' =>
                                [
                                    'route' => '/testhybrid',
                                    'defaults' =>
                                        [
                                            'controller' => 'Auth/SocialProfiles',
                                            'action' => 'testhybrid',
                                        ],
                                ],
                        ],
                ],
        ],
    'filters' =>
        [
            'aliases' =>
                [
                    'alnum' => 'Laminas\\I18n\\Filter\\Alnum',
                    'Alnum' => 'Laminas\\I18n\\Filter\\Alnum',
                    'alpha' => 'Laminas\\I18n\\Filter\\Alpha',
                    'Alpha' => 'Laminas\\I18n\\Filter\\Alpha',
                    'numberformat' => 'Laminas\\I18n\\Filter\\NumberFormat',
                    'numberFormat' => 'Laminas\\I18n\\Filter\\NumberFormat',
                    'NumberFormat' => 'Laminas\\I18n\\Filter\\NumberFormat',
                    'numberparse' => 'Laminas\\I18n\\Filter\\NumberParse',
                    'numberParse' => 'Laminas\\I18n\\Filter\\NumberParse',
                    'NumberParse' => 'Laminas\\I18n\\Filter\\NumberParse',
                    'PaginationQuery/Organizations/Organization' => 'Organizations/PaginationQuery',
                ],
            'factories' =>
                [
                    'Laminas\\I18n\\Filter\\Alnum' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Filter\\Alpha' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Filter\\NumberFormat' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Filter\\NumberParse' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Core/XssFilter' => 'Core\\Filter\\XssFilterFactory',
                    'Core/HtmlAbsPathFilter' => 'Core\\Factory\\Filter\\HtmlAbsPathFilterFactory',
                    'Cv/PaginationQuery' => 'Cv\\Repository\\Filter\\PaginationQueryFactory',
                    'PaginationQuery/Applications' => '\\Applications\\Repository\\Filter\\PaginationQueryFactory',
                    'Jobs/PaginationQuery' => 'Jobs\\Factory\\Repository\\Filter\\PaginationQueryFactory',
                    'Jobs/ChannelPrices' => 'Jobs\\Factory\\Filter\\ChannelPricesFactory',
                    'Organizations/PaginationQuery' => '\\Organizations\\Repository\\Filter\\PaginationQueryFactory',
                ],
            'invokables' =>
                [
                    'Core/Repository/PropertyToKeywords' => 'Core\\Repository\\Filter\\PropertyToKeywords',
                    'Auth/StripQueryParams' => '\\Auth\\Filter\\StripQueryParams',
                    'Auth/Entity/UserToSearchResult' => '\\Auth\\Entity\\Filter\\UserToSearchResult',
                    'PaginationQuery/Auth/User' => 'Auth\\Repository\\Filter\\PaginationSearchUsers',
                    'Applications/ActionToStatus' => 'Applications\\Filter\\ActionToStatus',
                    'Jobs/PaginationAdminQuery' => 'Jobs\\Repository\\Filter\\PaginationAdminQuery',
                    'Settings/Filter/DisableElementsCapableFormSettings' => 'Settings\\Form\\Filter\\DisableElementsCapableFormSettings',
                ],
        ],
    'validators' =>
        [
            'aliases' =>
                [
                    'alnum' => 'Laminas\\I18n\\Validator\\Alnum',
                    'Alnum' => 'Laminas\\I18n\\Validator\\Alnum',
                    'alpha' => 'Laminas\\I18n\\Validator\\Alpha',
                    'Alpha' => 'Laminas\\I18n\\Validator\\Alpha',
                    'datetime' => 'Laminas\\I18n\\Validator\\DateTime',
                    'dateTime' => 'Laminas\\I18n\\Validator\\DateTime',
                    'DateTime' => 'Laminas\\I18n\\Validator\\DateTime',
                    'float' => 'Laminas\\I18n\\Validator\\IsFloat',
                    'Float' => 'Laminas\\I18n\\Validator\\IsFloat',
                    'int' => 'Laminas\\I18n\\Validator\\IsInt',
                    'Int' => 'Laminas\\I18n\\Validator\\IsInt',
                    'isfloat' => 'Laminas\\I18n\\Validator\\IsFloat',
                    'isFloat' => 'Laminas\\I18n\\Validator\\IsFloat',
                    'IsFloat' => 'Laminas\\I18n\\Validator\\IsFloat',
                    'isint' => 'Laminas\\I18n\\Validator\\IsInt',
                    'isInt' => 'Laminas\\I18n\\Validator\\IsInt',
                    'IsInt' => 'Laminas\\I18n\\Validator\\IsInt',
                    'phonenumber' => 'Laminas\\I18n\\Validator\\PhoneNumber',
                    'phoneNumber' => 'Laminas\\I18n\\Validator\\PhoneNumber',
                    'PhoneNumber' => 'Laminas\\I18n\\Validator\\PhoneNumber',
                    'postcode' => 'Laminas\\I18n\\Validator\\PostCode',
                    'postCode' => 'Laminas\\I18n\\Validator\\PostCode',
                    'PostCode' => 'Laminas\\I18n\\Validator\\PostCode',
                ],
            'factories' =>
                [
                    'Laminas\\I18n\\Validator\\Alnum' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Validator\\Alpha' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Validator\\DateTime' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Validator\\IsFloat' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Validator\\IsInt' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Validator\\PhoneNumber' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\Validator\\PostCode' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Auth/Form/UniqueGroupName' => 'Auth\\Form\\Validator\\UniqueGroupNameFactory',
                    'Jobs/Form/UniqueApplyId' => 'Jobs\\Form\\Validator\\UniqueApplyIdFactory',
                ],
            'invokables' =>
                [
                    'Applications/Application' => 'Applications\\Entity\\Validator\\Application',
                ],
        ],
    'view_helpers' =>
        [
            'aliases' =>
                [
                    'currencyformat' => 'Laminas\\I18n\\View\\Helper\\CurrencyFormat',
                    'currencyFormat' => 'Laminas\\I18n\\View\\Helper\\CurrencyFormat',
                    'CurrencyFormat' => 'Laminas\\I18n\\View\\Helper\\CurrencyFormat',
                    'dateformat' => 'Laminas\\I18n\\View\\Helper\\DateFormat',
                    'dateFormat' => 'Laminas\\I18n\\View\\Helper\\DateFormat',
                    'DateFormat' => 'Laminas\\I18n\\View\\Helper\\DateFormat',
                    'numberformat' => 'Laminas\\I18n\\View\\Helper\\NumberFormat',
                    'numberFormat' => 'Laminas\\I18n\\View\\Helper\\NumberFormat',
                    'NumberFormat' => 'Laminas\\I18n\\View\\Helper\\NumberFormat',
                    'plural' => 'Laminas\\I18n\\View\\Helper\\Plural',
                    'Plural' => 'Laminas\\I18n\\View\\Helper\\Plural',
                    'translate' => 'Laminas\\I18n\\View\\Helper\\Translate',
                    'Translate' => 'Laminas\\I18n\\View\\Helper\\Translate',
                    'translateplural' => 'Laminas\\I18n\\View\\Helper\\TranslatePlural',
                    'translatePlural' => 'Laminas\\I18n\\View\\Helper\\TranslatePlural',
                    'TranslatePlural' => 'Laminas\\I18n\\View\\Helper\\TranslatePlural',
                    'form' => 'Laminas\\Form\\View\\Helper\\Form',
                    'Form' => 'Laminas\\Form\\View\\Helper\\Form',
                    'formbutton' => 'Laminas\\Form\\View\\Helper\\FormButton',
                    'form_button' => 'Laminas\\Form\\View\\Helper\\FormButton',
                    'formButton' => 'Laminas\\Form\\View\\Helper\\FormButton',
                    'FormButton' => 'Laminas\\Form\\View\\Helper\\FormButton',
                    'formcaptcha' => 'Laminas\\Form\\View\\Helper\\FormCaptcha',
                    'form_captcha' => 'Laminas\\Form\\View\\Helper\\FormCaptcha',
                    'formCaptcha' => 'Laminas\\Form\\View\\Helper\\FormCaptcha',
                    'FormCaptcha' => 'Laminas\\Form\\View\\Helper\\FormCaptcha',
                    'captchadumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'captcha_dumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'captcha/dumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'CaptchaDumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'captchaDumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'formcaptchadumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'form_captcha_dumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'formCaptchaDumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'FormCaptchaDumb' => 'Laminas\\Form\\View\\Helper\\Captcha\\Dumb',
                    'captchafiglet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'captcha/figlet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'captcha_figlet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'captchaFiglet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'CaptchaFiglet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'formcaptchafiglet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'form_captcha_figlet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'formCaptchaFiglet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'FormCaptchaFiglet' => 'Laminas\\Form\\View\\Helper\\Captcha\\Figlet',
                    'captchaimage' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'captcha/image' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'captcha_image' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'captchaImage' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'CaptchaImage' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'formcaptchaimage' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'form_captcha_image' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'formCaptchaImage' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'FormCaptchaImage' => 'Laminas\\Form\\View\\Helper\\Captcha\\Image',
                    'captcharecaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'captcha/recaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'captcha_recaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'captchaRecaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'CaptchaRecaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'formcaptcharecaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'form_captcha_recaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'formCaptchaRecaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'FormCaptchaRecaptcha' => 'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha',
                    'formcheckbox' => 'Laminas\\Form\\View\\Helper\\FormCheckbox',
                    'form_checkbox' => 'Laminas\\Form\\View\\Helper\\FormCheckbox',
                    'formCheckbox' => 'Laminas\\Form\\View\\Helper\\FormCheckbox',
                    'FormCheckbox' => 'Laminas\\Form\\View\\Helper\\FormCheckbox',
                    'formcollection' => 'Laminas\\Form\\View\\Helper\\FormCollection',
                    'form_collection' => 'Laminas\\Form\\View\\Helper\\FormCollection',
                    'formCollection' => 'Laminas\\Form\\View\\Helper\\FormCollection',
                    'FormCollection' => 'Laminas\\Form\\View\\Helper\\FormCollection',
                    'formcolor' => 'Laminas\\Form\\View\\Helper\\FormColor',
                    'form_color' => 'Laminas\\Form\\View\\Helper\\FormColor',
                    'formColor' => 'Laminas\\Form\\View\\Helper\\FormColor',
                    'FormColor' => 'Laminas\\Form\\View\\Helper\\FormColor',
                    'formdate' => 'Laminas\\Form\\View\\Helper\\FormDate',
                    'form_date' => 'Laminas\\Form\\View\\Helper\\FormDate',
                    'formDate' => 'Laminas\\Form\\View\\Helper\\FormDate',
                    'FormDate' => 'Laminas\\Form\\View\\Helper\\FormDate',
                    'formdatetime' => 'Laminas\\Form\\View\\Helper\\FormDateTime',
                    'form_date_time' => 'Laminas\\Form\\View\\Helper\\FormDateTime',
                    'formDateTime' => 'Laminas\\Form\\View\\Helper\\FormDateTime',
                    'FormDateTime' => 'Laminas\\Form\\View\\Helper\\FormDateTime',
                    'formdatetimelocal' => 'Laminas\\Form\\View\\Helper\\FormDateTimeLocal',
                    'form_date_time_local' => 'Laminas\\Form\\View\\Helper\\FormDateTimeLocal',
                    'formDateTimeLocal' => 'Laminas\\Form\\View\\Helper\\FormDateTimeLocal',
                    'FormDateTimeLocal' => 'Laminas\\Form\\View\\Helper\\FormDateTimeLocal',
                    'formdatetimeselect' => 'Laminas\\Form\\View\\Helper\\FormDateTimeSelect',
                    'form_date_time_select' => 'Laminas\\Form\\View\\Helper\\FormDateTimeSelect',
                    'formDateTimeSelect' => 'Laminas\\Form\\View\\Helper\\FormDateTimeSelect',
                    'FormDateTimeSelect' => 'Laminas\\Form\\View\\Helper\\FormDateTimeSelect',
                    'formdateselect' => 'Laminas\\Form\\View\\Helper\\FormDateSelect',
                    'form_date_select' => 'Laminas\\Form\\View\\Helper\\FormDateSelect',
                    'formDateSelect' => 'Laminas\\Form\\View\\Helper\\FormDateSelect',
                    'FormDateSelect' => 'Laminas\\Form\\View\\Helper\\FormDateSelect',
                    'form_element' => 'formElement',
                    'formelement' => 'Laminas\\Form\\View\\Helper\\FormElement',
                    'formElement' => 'Laminas\\Form\\View\\Helper\\FormElement',
                    'FormElement' => 'Laminas\\Form\\View\\Helper\\FormElement',
                    'form_element_errors' => 'Laminas\\Form\\View\\Helper\\FormElementErrors',
                    'formelementerrors' => 'Laminas\\Form\\View\\Helper\\FormElementErrors',
                    'formElementErrors' => 'Laminas\\Form\\View\\Helper\\FormElementErrors',
                    'FormElementErrors' => 'Laminas\\Form\\View\\Helper\\FormElementErrors',
                    'form_email' => 'Laminas\\Form\\View\\Helper\\FormEmail',
                    'formemail' => 'Laminas\\Form\\View\\Helper\\FormEmail',
                    'formEmail' => 'Laminas\\Form\\View\\Helper\\FormEmail',
                    'FormEmail' => 'Laminas\\Form\\View\\Helper\\FormEmail',
                    'form_file' => 'Laminas\\Form\\View\\Helper\\FormFile',
                    'formfile' => 'Laminas\\Form\\View\\Helper\\FormFile',
                    'formFile' => 'Laminas\\Form\\View\\Helper\\FormFile',
                    'FormFile' => 'Laminas\\Form\\View\\Helper\\FormFile',
                    'formfileapcprogress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileApcProgress',
                    'form_file_apc_progress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileApcProgress',
                    'formFileApcProgress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileApcProgress',
                    'FormFileApcProgress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileApcProgress',
                    'formfilesessionprogress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileSessionProgress',
                    'form_file_session_progress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileSessionProgress',
                    'formFileSessionProgress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileSessionProgress',
                    'FormFileSessionProgress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileSessionProgress',
                    'formfileuploadprogress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileUploadProgress',
                    'form_file_upload_progress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileUploadProgress',
                    'formFileUploadProgress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileUploadProgress',
                    'FormFileUploadProgress' => 'Laminas\\Form\\View\\Helper\\File\\FormFileUploadProgress',
                    'formhidden' => 'Laminas\\Form\\View\\Helper\\FormHidden',
                    'form_hidden' => 'Laminas\\Form\\View\\Helper\\FormHidden',
                    'formHidden' => 'Laminas\\Form\\View\\Helper\\FormHidden',
                    'FormHidden' => 'Laminas\\Form\\View\\Helper\\FormHidden',
                    'formimage' => 'Laminas\\Form\\View\\Helper\\FormImage',
                    'form_image' => 'Laminas\\Form\\View\\Helper\\FormImage',
                    'formImage' => 'Laminas\\Form\\View\\Helper\\FormImage',
                    'FormImage' => 'Laminas\\Form\\View\\Helper\\FormImage',
                    'forminput' => 'Laminas\\Form\\View\\Helper\\FormInput',
                    'form_input' => 'Laminas\\Form\\View\\Helper\\FormInput',
                    'formInput' => 'Laminas\\Form\\View\\Helper\\FormInput',
                    'FormInput' => 'Laminas\\Form\\View\\Helper\\FormInput',
                    'formlabel' => 'Laminas\\Form\\View\\Helper\\FormLabel',
                    'form_label' => 'Laminas\\Form\\View\\Helper\\FormLabel',
                    'formLabel' => 'Laminas\\Form\\View\\Helper\\FormLabel',
                    'FormLabel' => 'Laminas\\Form\\View\\Helper\\FormLabel',
                    'formmonth' => 'Laminas\\Form\\View\\Helper\\FormMonth',
                    'form_month' => 'Laminas\\Form\\View\\Helper\\FormMonth',
                    'formMonth' => 'Laminas\\Form\\View\\Helper\\FormMonth',
                    'FormMonth' => 'Laminas\\Form\\View\\Helper\\FormMonth',
                    'formmonthselect' => 'Laminas\\Form\\View\\Helper\\FormMonthSelect',
                    'form_month_select' => 'Laminas\\Form\\View\\Helper\\FormMonthSelect',
                    'formMonthSelect' => 'Laminas\\Form\\View\\Helper\\FormMonthSelect',
                    'FormMonthSelect' => 'Laminas\\Form\\View\\Helper\\FormMonthSelect',
                    'formmulticheckbox' => 'Laminas\\Form\\View\\Helper\\FormMultiCheckbox',
                    'form_multi_checkbox' => 'Laminas\\Form\\View\\Helper\\FormMultiCheckbox',
                    'formMultiCheckbox' => 'Laminas\\Form\\View\\Helper\\FormMultiCheckbox',
                    'FormMultiCheckbox' => 'Laminas\\Form\\View\\Helper\\FormMultiCheckbox',
                    'formnumber' => 'Laminas\\Form\\View\\Helper\\FormNumber',
                    'form_number' => 'Laminas\\Form\\View\\Helper\\FormNumber',
                    'formNumber' => 'Laminas\\Form\\View\\Helper\\FormNumber',
                    'FormNumber' => 'Laminas\\Form\\View\\Helper\\FormNumber',
                    'formpassword' => 'Laminas\\Form\\View\\Helper\\FormPassword',
                    'form_password' => 'Laminas\\Form\\View\\Helper\\FormPassword',
                    'formPassword' => 'Laminas\\Form\\View\\Helper\\FormPassword',
                    'FormPassword' => 'Laminas\\Form\\View\\Helper\\FormPassword',
                    'formradio' => 'Laminas\\Form\\View\\Helper\\FormRadio',
                    'form_radio' => 'Laminas\\Form\\View\\Helper\\FormRadio',
                    'formRadio' => 'Laminas\\Form\\View\\Helper\\FormRadio',
                    'FormRadio' => 'Laminas\\Form\\View\\Helper\\FormRadio',
                    'formrange' => 'Laminas\\Form\\View\\Helper\\FormRange',
                    'form_range' => 'Laminas\\Form\\View\\Helper\\FormRange',
                    'formRange' => 'Laminas\\Form\\View\\Helper\\FormRange',
                    'FormRange' => 'Laminas\\Form\\View\\Helper\\FormRange',
                    'formreset' => 'Laminas\\Form\\View\\Helper\\FormReset',
                    'form_reset' => 'Laminas\\Form\\View\\Helper\\FormReset',
                    'formReset' => 'Laminas\\Form\\View\\Helper\\FormReset',
                    'FormReset' => 'Laminas\\Form\\View\\Helper\\FormReset',
                    'formrow' => 'Laminas\\Form\\View\\Helper\\FormRow',
                    'form_row' => 'Laminas\\Form\\View\\Helper\\FormRow',
                    'formRow' => 'Laminas\\Form\\View\\Helper\\FormRow',
                    'FormRow' => 'Laminas\\Form\\View\\Helper\\FormRow',
                    'formsearch' => 'Laminas\\Form\\View\\Helper\\FormSearch',
                    'form_search' => 'Laminas\\Form\\View\\Helper\\FormSearch',
                    'formSearch' => 'Laminas\\Form\\View\\Helper\\FormSearch',
                    'FormSearch' => 'Laminas\\Form\\View\\Helper\\FormSearch',
                    'formselect' => 'Laminas\\Form\\View\\Helper\\FormSelect',
                    'form_select' => 'Laminas\\Form\\View\\Helper\\FormSelect',
                    'formSelect' => 'Laminas\\Form\\View\\Helper\\FormSelect',
                    'FormSelect' => 'Laminas\\Form\\View\\Helper\\FormSelect',
                    'formsubmit' => 'Laminas\\Form\\View\\Helper\\FormSubmit',
                    'form_submit' => 'Laminas\\Form\\View\\Helper\\FormSubmit',
                    'formSubmit' => 'Laminas\\Form\\View\\Helper\\FormSubmit',
                    'FormSubmit' => 'Laminas\\Form\\View\\Helper\\FormSubmit',
                    'formtel' => 'Laminas\\Form\\View\\Helper\\FormTel',
                    'form_tel' => 'Laminas\\Form\\View\\Helper\\FormTel',
                    'formTel' => 'Laminas\\Form\\View\\Helper\\FormTel',
                    'FormTel' => 'Laminas\\Form\\View\\Helper\\FormTel',
                    'formtext' => 'Laminas\\Form\\View\\Helper\\FormText',
                    'form_text' => 'Laminas\\Form\\View\\Helper\\FormText',
                    'formText' => 'Laminas\\Form\\View\\Helper\\FormText',
                    'FormText' => 'Laminas\\Form\\View\\Helper\\FormText',
                    'formtextarea' => 'Laminas\\Form\\View\\Helper\\FormTextarea',
                    'form_text_area' => 'Laminas\\Form\\View\\Helper\\FormTextarea',
                    'formTextarea' => 'Laminas\\Form\\View\\Helper\\FormTextarea',
                    'formTextArea' => 'Laminas\\Form\\View\\Helper\\FormTextarea',
                    'FormTextArea' => 'Laminas\\Form\\View\\Helper\\FormTextarea',
                    'formtime' => 'Laminas\\Form\\View\\Helper\\FormTime',
                    'form_time' => 'Laminas\\Form\\View\\Helper\\FormTime',
                    'formTime' => 'Laminas\\Form\\View\\Helper\\FormTime',
                    'FormTime' => 'Laminas\\Form\\View\\Helper\\FormTime',
                    'formurl' => 'Laminas\\Form\\View\\Helper\\FormUrl',
                    'form_url' => 'Laminas\\Form\\View\\Helper\\FormUrl',
                    'formUrl' => 'Laminas\\Form\\View\\Helper\\FormUrl',
                    'FormUrl' => 'Laminas\\Form\\View\\Helper\\FormUrl',
                    'formweek' => 'Laminas\\Form\\View\\Helper\\FormWeek',
                    'form_week' => 'Laminas\\Form\\View\\Helper\\FormWeek',
                    'formWeek' => 'Laminas\\Form\\View\\Helper\\FormWeek',
                    'FormWeek' => 'Laminas\\Form\\View\\Helper\\FormWeek',
                    'snippet' => 'Core\\View\\Helper\\Snippet',
                    'ajaxUrl' => 'Core\\View\\Helper\\AjaxUrl',
                    'proxy' => 'Core\\View\\Helper\\Proxy',
                    'jsonLd' => 'Jobs\\View\\Helper\\JsonLd',
                ],
            'factories' =>
                [
                    'Laminas\\I18n\\View\\Helper\\CurrencyFormat' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\View\\Helper\\DateFormat' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\View\\Helper\\NumberFormat' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\View\\Helper\\Plural' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\View\\Helper\\Translate' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\I18n\\View\\Helper\\TranslatePlural' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\Form' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormButton' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormCaptcha' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\Captcha\\Dumb' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\Captcha\\Figlet' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\Captcha\\Image' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\Captcha\\ReCaptcha' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormCheckbox' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormCollection' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormColor' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormDate' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormDateTime' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormDateTimeLocal' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormDateTimeSelect' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormDateSelect' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormElement' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormElementErrors' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormEmail' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormFile' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\File\\FormFileApcProgress' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\File\\FormFileSessionProgress' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\File\\FormFileUploadProgress' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormHidden' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormImage' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormInput' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormLabel' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormMonth' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormMonthSelect' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormMultiCheckbox' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormNumber' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormPassword' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormRadio' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormRange' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormReset' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormRow' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormSearch' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormSelect' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormSubmit' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormTel' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormText' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormTextarea' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormTime' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormUrl' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Form\\View\\Helper\\FormWeek' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'params' => 'Core\\View\\Helper\\Service\\ParamsHelperFactory',
                    'socialButtons' => 'Core\\Factory\\View\\Helper\\SocialButtonsFactory',
                    'TinyMCEditorLight' => 'Core\\Factory\\Form\\View\\Helper\\FormEditorLightFactory',
                    'configHeadScript' => 'Core\\View\\Helper\\Service\\HeadScriptFactory',
                    'Core\\View\\Helper\\AjaxUrl' => 'Core\\Factory\\View\\Helper\\AjaxUrlFactory',
                    'services' =>
                        [
                            0 => 'Core\\View\\Helper\\Services',
                            1 => 'factory',
                        ],
                    'InsertFile' =>
                        [
                            0 => 'Core\\View\\Helper\\InsertFile',
                            1 => 'factory',
                        ],
                    'Core\\View\\Helper\\Snippet' => 'Core\\Factory\\View\\Helper\\SnippetFactory',
                    'Core\\View\\Helper\\Proxy' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'auth' => '\\Auth\\Factory\\View\\Helper\\AuthFactory',
                    'acl' => '\\Acl\\Factory\\View\\Helper\\AclFactory',
                    'applyUrl' => 'Jobs\\Factory\\View\\Helper\\ApplyUrlFactory',
                    'jobUrl' => 'Jobs\\Factory\\View\\Helper\\JobUrlFactory',
                    'Jobs/AdminEditLink' => 'Jobs\\Factory\\View\\Helper\\AdminEditLinkFactory',
                    'Jobs\\View\\Helper\\JsonLd' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                ],
            'invokables' =>
                [
                    'formElement' => 'Core\\Form\\View\\Helper\\FormElement',
                    'formLabel' => 'Core\\Form\\View\\Helper\\RequiredMarkInFormLabel',
                    'form' => 'Core\\Form\\View\\Helper\\Form',
                    'formSimple' => 'Core\\Form\\View\\Helper\\FormSimple',
                    'formContainer' => 'Core\\Form\\View\\Helper\\FormContainer',
                    'formWizardContainer' => 'Core\\Form\\View\\Helper\\FormWizardContainer',
                    'formCollectionContainer' => 'Core\\Form\\View\\Helper\\FormCollectionContainer',
                    'summaryForm' => 'Core\\Form\\View\\Helper\\SummaryForm',
                    'searchForm' => 'Core\\Form\\View\\Helper\\SearchForm',
                    'filterForm' => 'Core\\Form\\View\\Helper\\FilterForm',
                    'formPartial' => '\\Core\\Form\\View\\Helper\\FormPartial',
                    'formCollection' => 'Core\\Form\\View\\Helper\\FormCollection',
                    'formRow' => 'Core\\Form\\View\\Helper\\FormRow',
                    'formRowSimple' => 'Core\\Form\\View\\Helper\\FormSimpleRow',
                    'formRowCombined' => 'Core\\Form\\View\\Helper\\FormRowCombined',
                    'formFileUpload' => 'Core\\Form\\View\\Helper\\FormFileUpload',
                    'formImageUpload' => 'Core\\Form\\View\\Helper\\FormImageUpload',
                    'formimageupload' => 'Core\\Form\\View\\Helper\\FormImageUpload',
                    'formCheckBox' => 'Core\\Form\\View\\Helper\\FormCheckbox',
                    'formcheckbox' => 'Core\\Form\\View\\Helper\\FormCheckbox',
                    'formDatePicker' => 'Core\\Form\\View\\Helper\\FormDatePicker',
                    'formInfoCheckBox' => 'Core\\Form\\View\\Helper\\FormInfoCheckbox',
                    'formSelect' => 'Core\\Form\\View\\Helper\\FormSelect',
                    'dateFormat' => 'Core\\View\\Helper\\DateFormat',
                    'salutation' => 'Core\\View\\Helper\\Salutation',
                    'period' => 'Core\\View\\Helper\\Period',
                    'link' => 'Core\\View\\Helper\\Link',
                    'languageSwitcher' => 'Core\\View\\Helper\\LanguageSwitcher',
                    'rating' => 'Core\\View\\Helper\\Rating',
                    'base64' => 'Core\\View\\Helper\\Base64',
                    'alert' => 'Core\\View\\Helper\\Alert',
                    'spinnerButton' => 'Core\\Form\\View\\Helper\\Element\\SpinnerButton',
                    'toggleButton' => 'Core\\Form\\View\\Helper\\ToggleButton',
                    'TinyMCEditor' => 'Core\\Form\\View\\Helper\\FormEditor',
                    'TinyMCEditorColor' => 'Core\\Form\\View\\Helper\\FormEditorColor',
                    'buildReferer' => '\\Auth\\View\\Helper\\BuildReferer',
                    'loginInfo' => '\\Auth\\View\\Helper\\LoginInfo',
                    'jobPreviewLink' => 'Jobs\\Form\\View\\Helper\\PreviewLink',
                    'jobApplyButtons' => 'Jobs\\View\\Helper\\ApplyButtons',
                    'Settings/FormDisableElementsCapableFormSettings' => 'Settings\\Form\\View\\Helper\\FormDisableElementsCapableFormSettings',
                ],
            'initializers' =>
                [
                ],
        ],
    'controller_plugins' =>
        [
            'aliases' =>
                [
                    'prg' => 'Laminas\\Mvc\\Plugin\\Prg\\PostRedirectGet',
                    'PostRedirectGet' => 'Laminas\\Mvc\\Plugin\\Prg\\PostRedirectGet',
                    'postRedirectGet' => 'Laminas\\Mvc\\Plugin\\Prg\\PostRedirectGet',
                    'postredirectget' => 'Laminas\\Mvc\\Plugin\\Prg\\PostRedirectGet',
                    'Laminas\\Mvc\\Controller\\Plugin\\PostRedirectGet' => 'Laminas\\Mvc\\Plugin\\Prg\\PostRedirectGet',
                    'identity' => 'Laminas\\Mvc\\Plugin\\Identity\\Identity',
                    'Identity' => 'Laminas\\Mvc\\Plugin\\Identity\\Identity',
                    'Laminas\\Mvc\\Controller\\Plugin\\Identity' => 'Laminas\\Mvc\\Plugin\\Identity\\Identity',
                    'flashmessenger' => 'Laminas\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
                    'flashMessenger' => 'Laminas\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
                    'FlashMessenger' => 'Laminas\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
                    'Laminas\\Mvc\\Controller\\Plugin\\FlashMessenger' => 'Laminas\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
                    'CreateConsoleNotFoundModel' => 'Laminas\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
                    'createConsoleNotFoundModel' => 'Laminas\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
                    'createconsolenotfoundmodel' => 'Laminas\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
                    'Laminas\\Mvc\\Controller\\Plugin\\CreateConsoleNotFoundModel::class' => 'Laminas\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
                    'filesender' => 'Core/FileSender',
                    'mailer' => 'Core/Mailer',
                    'Mailer' => 'Core/Mailer',
                    'pagination' => 'Core/PaginationBuilder',
                    'paginator' => 'Core/CreatePaginator',
                    'paginatorservice' => 'Core/PaginatorService',
                    'paginationParams' => 'Core/PaginationParams',
                    'searchform' => 'Core/SearchForm',
                    'notification' => 'Notification',
                    'acl' => 'Acl',
                    'auth' => 'Auth',
                ],
            'factories' =>
                [
                    'Laminas\\Mvc\\Plugin\\Prg\\PostRedirectGet' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Mvc\\Plugin\\Identity\\Identity' => 'Laminas\\Mvc\\Plugin\\Identity\\IdentityFactory',
                    'Laminas\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Laminas\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'config' => 'Core\\Controller\\Plugin\\ConfigFactory',
                    'Notification' => '\\Core\\Controller\\Plugin\\Service\\NotificationFactory',
                    'entitysnapshot' => 'Core\\Controller\\Plugin\\Service\\EntitySnapshotFactory',
                    'Core/SearchForm' => 'Core\\Factory\\Controller\\Plugin\\SearchFormFactory',
                    'listquery' => 'Core\\Controller\\Plugin\\ListQuery::factory',
                    'mail' => 'Core\\Controller\\Plugin\\Mail::factory',
                    'Core/Mailer' =>
                        [
                            0 => 'Core\\Controller\\Plugin\\Mailer',
                            1 => 'factory',
                        ],
                    'Core/CreatePaginator' =>
                        [
                            0 => 'Core\\Controller\\Plugin\\CreatePaginator',
                            1 => 'factory',
                        ],
                    'Core/PaginatorService' =>
                        [
                            0 => 'Core\\Controller\\Plugin\\CreatePaginatorService',
                            1 => 'factory',
                        ],
                    'Auth/SocialProfiles' => 'Auth\\Controller\\Plugin\\Service\\SocialProfilesFactory',
                    'Acl' => '\\Acl\\Controller\\Plugin\\AclFactory',
                    'Auth/LoginFilter' => 'Auth\\Controller\\Plugin\\LoginFilter::factory',
                    'OAuth' =>
                        [
                            0 => 'Auth\\Controller\\Plugin\\OAuth',
                            1 => 'factory',
                        ],
                    'Auth' =>
                        [
                            0 => 'Auth\\Controller\\Plugin\\Auth',
                            1 => 'factory',
                        ],
                    'Auth/User/Switcher' => 'Auth\\Factory\\Controller\\Plugin\\UserSwitcherFactory',
                    'initializeJob' => 'Jobs\\Factory\\Controller\\Plugin\\InitializeJobFactory',
                    'settings' => '\\Settings\\Controller\\Plugin\\SettingsFactory',
                    'Organizations/InvitationHandler' => 'Organizations\\Factory\\Controller\\Plugin\\InvitationHandlerFactory',
                    'Organizations/AcceptInvitationHandler' => 'Organizations\\Factory\\Controller\\Plugin\\AcceptInvitationHandlerFactory',
                    'Organizations/GetOrganizationHandler' => 'Organizations\\Factory\\Controller\\Plugin\\GetOrganizationHandlerFactory',
                ],
            'invokables' =>
                [
                    'Core/FileSender' => 'Core\\Controller\\Plugin\\FileSender',
                    'Core/ContentCollector' => 'Core\\Controller\\Plugin\\ContentCollector',
                    'Core/PaginationParams' => 'Core\\Controller\\Plugin\\PaginationParams',
                    'Core/PaginationBuilder' => 'Core\\Controller\\Plugin\\PaginationBuilder',
                ],
            'shared' =>
                [
                    'OAuth' => false,
                ],
        ],
    'console' =>
        [
            'router' =>
                [
                    'routes' =>
                        [
                            'doctrine_cli' =>
                                [
                                    'type' => 'symfony_cli',
                                ],
                            'applications-keywords' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'applications generatekeywords [--filter=]',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Applications/Console',
                                                    'action' => 'generatekeywords',
                                                ],
                                        ],
                                ],
                            'applications-rating' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'applications calculate-rating [--filter=]',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Applications/Console',
                                                    'action' => 'calculateRating',
                                                ],
                                        ],
                                ],
                            'applications-cleanup' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'applications cleanup [--limit=]',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Applications/Console',
                                                    'action' => 'cleanup',
                                                ],
                                        ],
                                ],
                            'applications-partials' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'applications list',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Applications/Console',
                                                    'action' => 'listviewscripts',
                                                ],
                                        ],
                                ],
                            'applications-reset-files-permissions' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'applications reset-files-permissions [--filter=]',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Applications/Console',
                                                    'action' => 'resetFilesPermissions',
                                                ],
                                        ],
                                ],
                            'jobs-expire' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'jobs expire [--days=] [--limit=] [--info]',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Jobs/Console',
                                                    'action' => 'expirejobs',
                                                    'days' => 30,
                                                    'limit' => '10,0',
                                                ],
                                        ],
                                ],
                            'jobs-setpermissions' =>
                                [
                                    'options' =>
                                        [
                                            'route' => 'jobs setpermissions',
                                            'defaults' =>
                                                [
                                                    'controller' => 'Jobs/Console',
                                                    'action' => 'setpermissions',
                                                ],
                                        ],
                                ],
                        ],
                ],
        ],
    'doctrine' =>
        [
            'cache' =>
                [
                    'apc' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\ApcCache',
                            'namespace' => 'DoctrineModule',
                        ],
                    'apcu' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\ApcuCache',
                            'namespace' => 'DoctrineModule',
                        ],
                    'array' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\ArrayCache',
                            'namespace' => 'DoctrineModule',
                        ],
                    'filesystem' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\FilesystemCache',
                            'directory' => 'data/DoctrineModule/cache',
                            'namespace' => 'DoctrineModule',
                        ],
                    'memcache' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\MemcacheCache',
                            'instance' => 'my_memcache_alias',
                            'namespace' => 'DoctrineModule',
                        ],
                    'memcached' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\MemcachedCache',
                            'instance' => 'my_memcached_alias',
                            'namespace' => 'DoctrineModule',
                        ],
                    'predis' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\PredisCache',
                            'instance' => 'my_predis_alias',
                            'namespace' => 'DoctrineModule',
                        ],
                    'redis' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\RedisCache',
                            'instance' => 'my_redis_alias',
                            'namespace' => 'DoctrineModule',
                        ],
                    'wincache' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\WinCacheCache',
                            'namespace' => 'DoctrineModule',
                        ],
                    'xcache' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\XcacheCache',
                            'namespace' => 'DoctrineModule',
                        ],
                    'zenddata' =>
                        [
                            'class' => 'Doctrine\\Common\\Cache\\ZendDataCache',
                            'namespace' => 'DoctrineModule',
                        ],
                ],
            'authentication' =>
                [
                    'odm_default' =>
                        [
                            'objectManager' => 'doctrine.documentmanager.odm_default',
                            'identityClass' => 'Application\\Model\\User',
                            'identityProperty' => 'username',
                            'credentialProperty' => 'password',
                        ],
                    'orm_default' =>
                        [
                        ],
                ],
            'authenticationadapter' =>
                [
                    'odm_default' => true,
                    'orm_default' => true,
                ],
            'authenticationstorage' =>
                [
                    'odm_default' => true,
                    'orm_default' => true,
                ],
            'authenticationservice' =>
                [
                    'odm_default' => true,
                    'orm_default' => true,
                ],
            'connection' =>
                [
                    'odm_default' =>
                        [
                            'server' => 'localhost',
                            'port' => '27017',
                            'connectionString' => 'mongodb://172.19.0.1:27017/YAWIK_TEST',
                            'user' => null,
                            'password' => null,
                            'dbname' => null,
                            'options' =>
                                [
                                ],
                        ],
                ],
            'configuration' =>
                [
                    'odm_default' =>
                        [
                            'metadata_cache' => 'array',
                            'driver' => 'odm_default',
                            'generate_proxies' => 1,
                            'proxy_dir' => 'cache/DoctrineMongoODMModule/Proxy',
                            'proxy_namespace' => 'DoctrineMongoODMModule\\Proxy',
                            'generate_hydrators' => 1,
                            'hydrator_dir' => 'cache/DoctrineMongoODMModule/Hydrator',
                            'hydrator_namespace' => 'DoctrineMongoODMModule\\Hydrator',
                            'generate_persistent_collections' => 1,
                            'persistent_collection_dir' => 'data/DoctrineMongoODMModule/PersistentCollection',
                            'persistent_collection_namespace' => 'DoctrineMongoODMModule\\PersistentCollection',
                            'persistent_collection_factory' => null,
                            'persistent_collection_generator' => null,
                            'default_db' => 'YAWIK_TEST',
                            'filters' =>
                                [
                                ],
                            'types' =>
                                [
                                ],
                        ],
                ],
            'driver' =>
                [
                    'odm_default' =>
                        [
                            'class' => 'Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriverChain',
                            'drivers' =>
                                [
                                    'Core\\Entity' => 'annotation',
                                    'Auth\\Entity' => 'annotation',
                                    'Cv\\Entity' => 'annotation',
                                    'Applications\\Entity' => 'annotation',
                                    'Jobs\\Entity' => 'annotation',
                                    'Settings\\Entity' => 'annotation',
                                    'Geo\\Entity' => 'annotation',
                                    'Organizations\\Entity' => 'annotation',
                                ],
                        ],
                    'annotation' =>
                        [
                            'class' => 'Doctrine\\ODM\\MongoDB\\Mapping\\Driver\\AnnotationDriver',
                            'paths' =>
                                [
                                    0 => '/var/www/yawik/module/Auth/config/../src/Auth/Entity',
                                    1 => '/var/www/yawik/module/Cv/config/../src/Cv/Entity',
                                    2 => '/var/www/yawik/module/Applications/config/../src/Applications/Entity',
                                    3 => '/var/www/yawik/module/Jobs/config/../src/Jobs/Entity',
                                    4 => '/var/www/yawik/module/Geo/config/../src/Geo/Entity',
                                    5 => '/var/www/yawik/module/Organizations/config/../src/Organizations/Entity',
                                ],
                        ],
                ],
            'documentmanager' =>
                [
                    'odm_default' =>
                        [
                            'connection' => 'odm_default',
                            'configuration' => 'odm_default',
                            'eventmanager' => 'odm_default',
                        ],
                ],
            'eventmanager' =>
                [
                    'odm_default' =>
                        [
                            'subscribers' =>
                                [
                                    0 => 'Core/DoctrineMongoODM/RepositoryEvents',
                                    1 => '\\Core\\Repository\\DoctrineMongoODM\\Event\\GenerateSearchKeywordsListener',
                                    2 => '\\Core\\Repository\\DoctrineMongoODM\\Event\\PreUpdateDocumentsSubscriber',
                                    3 => '\\Cv\\Repository\\Event\\InjectContactListener',
                                    4 => '\\Cv\\Repository\\Event\\DeleteRemovedAttachmentsSubscriber',
                                    5 => '\\Cv\\Repository\\Event\\UpdateFilesPermissionsSubscriber',
                                    6 => '\\Applications\\Repository\\Event\\JobReferencesUpdateListener',
                                    7 => '\\Applications\\Repository\\Event\\UpdatePermissionsSubscriber',
                                    8 => '\\Applications\\Repository\\Event\\UpdateFilesPermissionsSubscriber',
                                    9 => '\\Applications\\Repository\\Event\\DeleteRemovedAttachmentsSubscriber',
                                    10 => '\\Jobs\\Repository\\Event\\UpdatePermissionsSubscriber',
                                    11 => 'Settings/InjectEntityResolverListener',
                                    12 => '\\Organizations\\Repository\\Event\\InjectOrganizationReferenceListener',
                                    13 => 'Organizations\\ImageFileCache\\ODMListener',
                                ],
                        ],
                ],
            'mongo_logger_collector' =>
                [
                    'odm_default' =>
                        [
                        ],
                ],
        ],
    'doctrine_factories' =>
        [
            'cache' => 'DoctrineModule\\Service\\CacheFactory',
            'eventmanager' => 'DoctrineModule\\Service\\EventManagerFactory',
            'driver' => 'DoctrineModule\\Service\\DriverFactory',
            'authenticationadapter' => 'DoctrineModule\\Service\\Authentication\\AdapterFactory',
            'authenticationstorage' => 'DoctrineModule\\Service\\Authentication\\StorageFactory',
            'authenticationservice' => 'DoctrineModule\\Service\\Authentication\\AuthenticationServiceFactory',
        ],
    'controllers' =>
        [
            'factories' =>
                [
                    'DoctrineModule\\Controller\\Cli' => 'DoctrineModule\\Service\\CliControllerFactory',
                    'Core\\Controller\\Index' =>
                        [
                            0 => 'Core\\Controller\\IndexController',
                            1 => 'factory',
                        ],
                    'Core/Admin' =>
                        [
                            0 => 'Core\\Controller\\AdminController',
                            1 => 'factory',
                        ],
                    'Core\\Controller\\File' =>
                        [
                            0 => 'Core\\Controller\\FileController',
                            1 => 'factory',
                        ],
                    'Auth\\Controller\\Manage' =>
                        [
                            0 => 'Auth\\Controller\\ManageController',
                            1 => 'factory',
                        ],
                    'Auth/ManageGroups' =>
                        [
                            0 => 'Auth\\Controller\\ManageGroupsController',
                            1 => 'factory',
                        ],
                    'Auth\\Controller\\ForgotPassword' => 'Auth\\Factory\\Controller\\ForgotPasswordControllerFactory',
                    'Auth\\Controller\\GotoResetPassword' => 'Auth\\Factory\\Controller\\GotoResetPasswordControllerFactory',
                    'Auth\\Controller\\Register' => 'Auth\\Factory\\Controller\\RegisterControllerFactory',
                    'Auth\\Controller\\RegisterConfirmation' => 'Auth\\Factory\\Controller\\RegisterConfirmationControllerFactory',
                    'Auth\\Controller\\Password' => 'Auth\\Factory\\Controller\\PasswordControllerFactory',
                    'Auth\\Controller\\Index' => 'Auth\\Factory\\Controller\\IndexControllerFactory',
                    'Auth/Users' => 'Auth\\Factory\\Controller\\UsersControllerFactory',
                    'Auth\\Controller\\Remove' => 'Auth\\Factory\\Controller\\RemoveControllerFactory',
                    'Cv\\Controller\\IndexController' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Cv/View' => 'Cv\\Factory\\Controller\\ViewControllerFactory',
                    'Cv\\Controller\\Manage' =>
                        [
                            0 => 'Cv\\Controller\\ManageController',
                            1 => 'factory',
                        ],
                    'Applications/Controller/Manage' =>
                        [
                            0 => 'Applications\\Controller\\ManageController',
                            1 => 'factory',
                        ],
                    'Applications\\Controller\\Apply' =>
                        [
                            0 => 'Applications\\Controller\\ApplyController',
                            1 => 'factory',
                        ],
                    'Applications/CommentController' =>
                        [
                            0 => 'Applications\\Controller\\CommentController',
                            1 => 'factory',
                        ],
                    'Applications/Console' =>
                        [
                            0 => 'Applications\\Controller\\ConsoleController',
                            1 => 'factory',
                        ],
                    'Jobs/Import' =>
                        [
                            0 => 'Jobs\\Controller\\ImportController',
                            1 => 'factory',
                        ],
                    'Jobs/Console' =>
                        [
                            0 => 'Jobs\\Controller\\ConsoleController',
                            1 => 'factory',
                        ],
                    'Jobs/AdminCategories' =>
                        [
                            0 => 'Jobs\\Controller\\AdminCategoriesController',
                            1 => 'factory',
                        ],
                    'Jobs/Admin' =>
                        [
                            0 => 'Jobs\\Controller\\AdminController',
                            1 => 'factory',
                        ],
                    'Jobs/Template' => 'Jobs\\Factory\\Controller\\TemplateControllerFactory',
                    'Jobs/Index' => 'Jobs\\Factory\\Controller\\IndexControllerFactory',
                    'Jobs/Approval' => 'Jobs\\Factory\\Controller\\ApprovalControllerFactory',
                    'Jobs/Jobboard' => 'Jobs\\Factory\\Controller\\JobboardControllerFactory',
                    'Jobs/AssignUser' => 'Jobs\\Factory\\Controller\\AssignUserControllerFactory',
                    'Jobs/ApiJobListByOrganization' => 'Jobs\\Factory\\Controller\\ApiJobListByOrganizationControllerFactory',
                    'Jobs/Manage' => 'Jobs\\Factory\\Controller\\ManageControllerFactory',
                    'Settings\\Controller\\Index' =>
                        [
                            0 => 'Settings\\Controller\\IndexController',
                            1 => 'factory',
                        ],
                    'Geo\\Controller\\Index' => 'Geo\\Factory\\Controller\\IndexControllerFactory',
                    'Organizations/InviteEmployee' =>
                        [
                            0 => 'Organizations\\Controller\\InviteEmployeeController',
                            1 => 'factory',
                        ],
                    'Organizations/Index' => 'Organizations\\Factory\\Controller\\IndexControllerFactory',
                ],
            'invokables' =>
                [
                    'Core\\Controller\\Content' => 'Core\\Controller\\ContentController',
                    'Auth\\Controller\\Image' => 'Auth\\Controller\\ImageController',
                    'Auth\\Controller\\HybridAuth' => 'Auth\\Controller\\HybridAuthController',
                    'Auth/SocialProfiles' => 'Auth\\Controller\\SocialProfilesController',
                    'Applications\\Controller\\Index' => 'Applications\\Controller\\IndexController',
                    'Applications\\Controller\\MultiManage' => 'Applications\\Controller\\MultimanageController',
                    'Jobs/ApiJobList' => 'Jobs\\Controller\\ApiJobListController',
                    'Jobs/ApiJobListByChannel' => 'Jobs\\Controller\\ApiJobListByChannelController',
                ],
            'abstract_factories' =>
                [
                    0 => 'Core\\Factory\\Controller\\LazyControllerFactory',
                ],
            'aliases' =>
                [
                    'Cv/Index' => 'Cv\\Controller\\IndexController',
                ],
        ],
    'hydrators' =>
        [
            'factories' =>
                [
                    'DoctrineModule\\Stdlib\\Hydrator\\DoctrineObject' => 'DoctrineMongoODMModule\\Service\\DoctrineObjectHydratorFactory',
                    'Hydrator\\Organization' => 'Organizations\\Entity\\Hydrator\\OrganizationHydratorFactory',
                    'Organizations/Logo' => 'Organizations\\Factory\\Entity\\Hydrator\\LogoHydratorFactory',
                ],
        ],
    'view_manager' =>
        [
            'template_map' =>
                [
                    'zend-developer-tools/toolbar/doctrine-odm' => '/var/www/yawik/vendor/doctrine/doctrine-mongo-odm-module/config/../view/zend-developer-tools/toolbar/doctrine-odm.phtml',
                    'noscript-notice' => '/var/www/yawik/module/Core/config/../view/layout/_noscript-notice.phtml',
                    'layout/layout' => '/var/www/yawik/module/Core/config/../view/layout/layout.phtml',
                    'error/404' => '/var/www/yawik/module/Core/config/../view/error/404.phtml',
                    'error/403' => '/var/www/yawik/module/Core/config/../view/error/403.phtml',
                    'error/index' => '/var/www/yawik/module/Core/config/../view/error/index.phtml',
                    'main-navigation' => '/var/www/yawik/module/Core/config/../view/partial/main-navigation.phtml',
                    'pagination-control' => '/var/www/yawik/module/Core/config/../view/partial/pagination-control.phtml',
                    'core/loading-popup' => '/var/www/yawik/module/Core/config/../view/partial/loading-popup.phtml',
                    'core/notifications' => '/var/www/yawik/module/Core/config/../view/partial/notifications.phtml',
                    'form/core/buttons' => '/var/www/yawik/module/Core/config/../view/form/buttons.phtml',
                    'core/social-buttons' => '/var/www/yawik/module/Core/config/../view/partial/social-buttons.phtml',
                    'form/core/privacy' => '/var/www/yawik/module/Core/config/../view/form/privacy.phtml',
                    'core/form/permissions-fieldset' => '/var/www/yawik/module/Core/config/../view/form/permissions-fieldset.phtml',
                    'core/form/permissions-collection' => '/var/www/yawik/module/Core/config/../view/form/permissions-collection.phtml',
                    'core/form/container-view' => '/var/www/yawik/module/Core/config/../view/form/container.view.phtml',
                    'core/form/tree-manage.view' => '/var/www/yawik/module/Core/config/../view/form/tree-manage.view.phtml',
                    'core/form/tree-manage.form' => '/var/www/yawik/module/Core/config/../view/form/tree-manage.form.phtml',
                    'core/form/tree-add-item' => '/var/www/yawik/module/Core/config/../view/form/tree-add-item.phtml',
                    'mail/header' => '/var/www/yawik/module/Core/config/../view/mail/header.phtml',
                    'mail/footer' => '/var/www/yawik/module/Core/config/../view/mail/footer.phtml',
                    'mail/footer.en' => '/var/www/yawik/module/Core/config/../view/mail/footer.en.phtml',
                    'form/auth/contact.form' => '/var/www/yawik/module/Auth/config/../view/form/contact.form.phtml',
                    'form/auth/contact.view' => '/var/www/yawik/module/Auth/config/../view/form/contact.view.phtml',
                    'form/auth/status.form' => '/var/www/yawik/module/Auth/config/../view/form/status.form.phtml',
                    'form/auth/status.view' => '/var/www/yawik/module/Auth/config/../view/form/status.view.phtml',
                    'auth/error/social-profiles-unconfigured' => '/var/www/yawik/module/Auth/config/../view/error/social-profiles-unconfigured.phtml',
                    'auth/form/user-info-container' => '/var/www/yawik/module/Auth/config/../view/form/user-info-container.phtml',
                    'auth/form/user-status-container' => '/var/www/yawik/module/Auth/config/../view/form/user-status-container.phtml',
                    'auth/form/userselect' => '/var/www/yawik/module/Auth/config/../view/form/userselect.phtml',
                    'auth/form/social-profiles-fieldset' => '/var/www/yawik/module/Auth/config/../view/form/social-profiles-fieldset.phtml',
                    'auth/form/social-profiles-button' => '/var/www/yawik/module/Auth/config/../view/form/social-profiles-button.phtml',
                    'auth/sidebar/groups-menu' => '/var/www/yawik/module/Auth/config/../view/sidebar/groups-menu.phtml',
                    'mail/first-external-login' => '/var/www/yawik/module/Auth/config/../view/mail/first-external-login.phtml',
                    'mail/first-socialmedia-login' => '/var/www/yawik/module/Auth/config/../view/mail/first-socialmedia-login.phtml',
                    'mail/forgotPassword' => '/var/www/yawik/module/Auth/config/../view/mail/forgot-password.phtml',
                    'mail/forgotPassword.en' => '/var/www/yawik/module/Auth/config/../view/mail/forgot-password.en.phtml',
                    'mail/register' => '/var/www/yawik/module/Auth/config/../view/mail/register.phtml',
                    'auth/mail/new-registration' => '/var/www/yawik/module/Auth/config/../view/mail/new-registration.phtml',
                    'auth/mail/new-registration.de' => '/var/www/yawik/module/Auth/config/../view/mail/new-registration.de.phtml',
                    'auth/mail/user-confirmed' => '/var/www/yawik/module/Auth/config/../view/mail/user-confirmed.phtml',
                    'auth/mail/user-confirmed.de' => '/var/www/yawik/module/Auth/config/../view/mail/user-confirmed.de.phtml',
                    'cv/form/employment.view' => '/var/www/yawik/module/Cv/config/../view/cv/form/employment.view.phtml',
                    'cv/form/employment.form' => '/var/www/yawik/module/Cv/config/../view/cv/form/employment.form.phtml',
                    'cv/form/education.view' => '/var/www/yawik/module/Cv/config/../view/cv/form/education.view.phtml',
                    'cv/form/education.form' => '/var/www/yawik/module/Cv/config/../view/cv/form/education.form.phtml',
                    'applications/error/not-found' => '/var/www/yawik/module/Applications/config/../view/error/not-found.phtml',
                    'layout/apply' => '/var/www/yawik/module/Applications/config/../view/layout/layout.phtml',
                    'applications/sidebar/manage' => '/var/www/yawik/module/Applications/config/../view/sidebar/manage.phtml',
                    'applications/mail/forward' => '/var/www/yawik/module/Applications/config/../view/mail/forward.phtml',
                    'applications/detail/pdf' => '/var/www/yawik/module/Applications/config/../view/applications/manage/detail.pdf.phtml',
                    'applications/index/disclaimer' => '/var/www/yawik/module/Applications/config/../view/applications/index/disclaimer.phtml',
                    'content/applications-privacy-policy' => '/var/www/yawik/module/Applications/config/../view/applications/index/disclaimer.phtml',
                    'jobs/form/list-filter' => '/var/www/yawik/module/Jobs/config/../view/form/list-filter.phtml',
                    'jobs/form/apply-identifier' => '/var/www/yawik/module/Jobs/config/../view/form/apply-identifier.phtml',
                    'jobs/form/hiring-organization-select' => '/var/www/yawik/module/Jobs/config/../view/form/hiring-organization-select.phtml',
                    'jobs/form/multiposting-select' => '/var/www/yawik/module/Jobs/config/../view/form/multiposting-select.phtml',
                    'jobs/form/multiposting-checkboxes' => '/var/www/yawik/module/Jobs/config/../view/form/multiposting-checkboxes.phtml',
                    'jobs/form/ats-mode.view' => '/var/www/yawik/module/Jobs/config/../view/form/ats-mode.view.phtml',
                    'jobs/form/ats-mode.form' => '/var/www/yawik/module/Jobs/config/../view/form/ats-mode.form.phtml',
                    'jobs/form/company-name-fieldset' => '/var/www/yawik/module/Jobs/config/../view/form/company-name-fieldset.phtml',
                    'jobs/form/preview' => '/var/www/yawik/module/Jobs/config/../view/form/preview.phtml',
                    'jobs/form/customer-note' => '/var/www/yawik/module/Jobs/config/../view/form/customer-note.phtml',
                    'jobs/partials/channel-list' => '/var/www/yawik/module/Jobs/config/../view/partials/channel-list.phtml',
                    'jobs/assign-user' => '/var/www/yawik/module/Jobs/config/../view/jobs/manage/assign-user.phtml',
                    'jobs/snapshot_or_preview' => '/var/www/yawik/module/Jobs/config/../view/partials/snapshot_or_preview.phtml',
                    'jobs/history' => '/var/www/yawik/module/Jobs/config/../view/partials/history.phtml',
                    'jobs/portalsummary' => '/var/www/yawik/module/Jobs/config/../view/partials/portalsummary.phtml',
                    'content/jobs-publish-on-yawik' => '/var/www/yawik/module/Jobs/config/../view/modals/yawik.phtml',
                    'content/jobs-publish-on-jobsintown' => '/var/www/yawik/module/Jobs/config/../view/modals/jobsintown.phtml',
                    'content/jobs-publish-on-homepage' => '/var/www/yawik/module/Jobs/config/../view/modals/homepage.phtml',
                    'content/jobs-publish-on-fazjob' => '/var/www/yawik/module/Jobs/config/../view/modals/fazjob.phtml',
                    'content/jobs-terms-and-conditions' => '/var/www/yawik/module/Jobs/config/../view/jobs/index/terms.phtml',
                    'mail/job-created' => '/var/www/yawik/module/Jobs/config/../view/mails/job-created.phtml',
                    'mail/job-pending' => '/var/www/yawik/module/Jobs/config/../view/mails/job-pending.phtml',
                    'mail/job-accepted' => '/var/www/yawik/module/Jobs/config/../view/mails/job-accepted.phtml',
                    'mail/job-rejected' => '/var/www/yawik/module/Jobs/config/../view/mails/job-rejected.phtml',
                    'mail/job-created.en' => '/var/www/yawik/module/Jobs/config/../view/mails/job-created.en.phtml',
                    'mail/job-pending.en' => '/var/www/yawik/module/Jobs/config/../view/mails/job-pending.en.phtml',
                    'mail/job-accepted.en' => '/var/www/yawik/module/Jobs/config/../view/mails/job-accepted.en.phtml',
                    'mail/job-rejected.en' => '/var/www/yawik/module/Jobs/config/../view/mails/job-rejected.en.phtml',
                    'jobs/error/no-parent' => '/var/www/yawik/module/Jobs/config/../view/error/no-parent.phtml',
                    'jobs/error/expired' => '/var/www/yawik/module/Jobs/config/../view/error/expired.phtml',
                    'pdf/application/details/button' => '/var/www/yawik/module/Pdf/config/../view/applicationDetailsButton.phtml',
                    'organizations/index/edit' => '/var/www/yawik/module/Organizations/config/../view/organizations/index/form.phtml',
                    'organizations/form/employees-fieldset' => '/var/www/yawik/module/Organizations/config/../view/form/employees-fieldset.phtml',
                    'organizations/form/employee-fieldset' => '/var/www/yawik/module/Organizations/config/../view/form/employee-fieldset.phtml',
                    'organizations/form/invite-employee-bar' => '/var/www/yawik/module/Organizations/config/../view/form/invite-employee-bar.phtml',
                    'organizations/error/no-parent' => '/var/www/yawik/module/Organizations/config/../view/error/no-parent.phtml',
                    'organizations/error/invite' => '/var/www/yawik/module/Organizations/config/../view/error/invite.phtml',
                    'organizations/mail/invite-employee' => '/var/www/yawik/module/Organizations/config/../view/mail/invite-employee.phtml',
                    'organizations/form/workflow-fieldset' => '/var/www/yawik/module/Organizations/config/../view/form/workflow-fieldset.phtml',
                ],
            'display_not_found_reason' => true,
            'display_exceptions' => true,
            'doctype' => 'HTML5',
            'not_found_template' => 'error/404',
            'unauthorized_template' => 'error/403',
            'exception_template' => 'error/index',
            'template_path_stack' =>
                [
                    0 => '/var/www/yawik/module/Core/config/../view',
                    'Auth' => '/var/www/yawik/module/Auth/config/../view',
                    1 => '/var/www/yawik/module/Cv/config/../view',
                    'Applications' => '/var/www/yawik/module/Applications/config/../view',
                    2 => '/var/www/yawik/module/Jobs/config/../view',
                    3 => '/var/www/yawik/module/Settings/config/../view',
                    4 => '/var/www/yawik/module/Organizations/config/../view',
                ],
        ],
    'laminas-developer-tools' =>
        [
            'profiler' =>
                [
                    'collectors' =>
                        [
                            'odm_default' => 'doctrine.mongo_logger_collector.odm_default',
                        ],
                ],
            'toolbar' =>
                [
                    'entries' =>
                        [
                            'odm_default' => 'zend-developer-tools/toolbar/doctrine-odm',
                        ],
                ],
        ],
    'options' =>
        [
            'Core/MailServiceOptions' =>
                [
                    'class' => '\\Core\\Options\\MailServiceOptions',
                    'options' =>
                        [
                            'name' => 'localhost',
                            'connectionClass' => 'login',
                            'host' => 'smtp.gmail.com',
                            'port' => 587,
                            'username' => 'test.yawik@gmail.com',
                            'password' => 'y4w1ktest',
                            'ssl' => 'tls',
                            'transportClass' => 'smtp',
                        ],
                ],
            'Auth/Options' =>
                [
                    'class' => 'Auth\\Options\\ModuleOptions',
                ],
            'Auth/CaptchaOptions' =>
                [
                    'class' => '\\Auth\\Options\\CaptchaOptions',
                ],
            'Cv/Options' =>
                [
                    'class' => '\\Cv\\Options\\ModuleOptions',
                ],
            'Jobs/JobboardSearchOptions' =>
                [
                    'class' => '\\Jobs\\Options\\JobboardSearchOptions',
                ],
            'Jobs/BaseFieldsetOptions' =>
                [
                    'class' => '\\Jobs\\Options\\BaseFieldsetOptions',
                ],
            'Geo/Options' =>
                [
                    'class' => '\\Geo\\Options\\ModuleOptions',
                ],
            'Organizations/ImageFileCacheOptions' =>
                [
                    'class' => '\\Organizations\\Options\\ImageFileCacheOptions',
                ],
            'Organizations\\Options\\OrganizationLogoOptions' =>
                [
                ],
        ],
    'Core' =>
        [
            'settings' =>
                [
                    'entity' => '\\Core\\Entity\\SettingsContainer',
                    'navigation_label' => 'general settings',
                    'navigation_class' => 'yk-icon yk-icon-settings',
                ],
        ],
    'log' =>
        [
            'Core/Log' =>
                [
                    'writers' =>
                        [
                            0 =>
                                [
                                    'name' => 'stream',
                                    'priority' => 1000,
                                    'options' =>
                                        [
                                            'stream' => '/var/www/yawik/module/Core/config/../../../log/yawik.log',
                                        ],
                                ],
                        ],
                ],
            'Log/Core/Mail' =>
                [
                    'writers' =>
                        [
                            0 =>
                                [
                                    'name' => 'stream',
                                    'priority' => 1000,
                                    'options' =>
                                        [
                                            'stream' => '/var/www/yawik/module/Core/config/../../../log/mails.log',
                                        ],
                                ],
                        ],
                ],
        ],
    'log_processors' =>
        [
            'invokables' =>
                [
                    'Core/UniqueId' => 'Core\\Log\\Processor\\UniqueId',
                ],
        ],
    'tracy' =>
        [
            'mode' => true,
            'bar' => false,
            'strict' => true,
            'log' => '/var/www/yawik/module/Core/config/../../../log/tracy',
            'email' => null,
            'email_snooze' => 900,
        ],
    'acl' =>
        [
            'rules' =>
                [
                    'guest' =>
                        [
                            'allow' =>
                                [
                                    'Entity/File' =>
                                        [
                                            '__ALL__' => 'Core/FileAccess',
                                        ],
                                    0 => 'route/lang/auth',
                                    1 => 'route/auth-provider',
                                    2 => 'route/auth-hauth',
                                    3 => 'route/auth-extern',
                                    4 => 'route/lang/forgot-password',
                                    5 => 'route/lang/goto-reset-password',
                                    6 => 'route/lang/register',
                                    7 => 'route/lang/register-confirmation',
                                    8 => 'route/lang/applications/detail',
                                    'Applications\\Controller\\Manage' => 'detail',
                                    'Entity/Application' =>
                                        [
                                            'read' => 'Applications/Access',
                                            'subsequentAttachmentUpload' => 'Applications/Access',
                                        ],
                                    9 => 'Jobboard',
                                    10 => 'Jobs/Jobboard',
                                    11 => 'Jobs/ApiJobListByChannel',
                                    'Jobs/Template' =>
                                        [
                                            0 => 'view',
                                            1 => 'edittemplate',
                                        ],
                                    'Jobs/Manage' =>
                                        [
                                            0 => 'template',
                                        ],
                                    12 => 'Jobs/ApiJobList',
                                    13 => 'Jobs/ApiJobListByOrganization',
                                    14 => 'route/lang/jobs/template',
                                    15 => 'route/lang/jobboard',
                                    16 => 'Entity/OrganizationImage',
                                    17 => 'route/lang/organizations/invite',
                                    'Organizations/InviteEmployee' =>
                                        [
                                            0 => 'accept',
                                        ],
                                ],
                            'deny' =>
                                [
                                    0 => 'route/lang/organizations',
                                    'Organizations/InviteEmployee' =>
                                        [
                                            0 => 'invite',
                                        ],
                                ],
                        ],
                    'admin' =>
                        [
                            'allow' =>
                                [
                                    0 => 'route/lang/admin',
                                    1 => '__ALL__',
                                    2 => 'Users',
                                    3 => 'route/lang/user-list',
                                    4 => 'route/lang/user-edit',
                                    'Auth/Users' => '__ALL__',
                                    5 => 'route/lang/jobs/approval',
                                    6 => 'route/auth-logout',
                                    7 => 'route/lang/my',
                                    8 => 'Jobboard',
                                    9 => 'route/lang/my-password',
                                    'Jobs/Manage' =>
                                        [
                                            0 => 'approval',
                                        ],
                                    10 => 'pendingJobs',
                                    'Entity/Jobs/Job' =>
                                        [
                                            0 => 'delete',
                                        ],
                                ],
                            'deny' =>
                                [
                                    0 => 'lang/jobs',
                                ],
                        ],
                    'user' =>
                        [
                            'allow' =>
                                [
                                    0 => 'route/auth-logout',
                                    1 => 'route/lang/my',
                                    2 => 'route/lang/my-password',
                                    3 => 'route/lang/user-remove',
                                    'Auth/Users' => 'switch',
                                    4 => 'route/lang/my-cv',
                                    5 => 'Cv\\Controller\\Manage',
                                    6 => 'navigation/resume-user',
                                    'Cv/Status' =>
                                        [
                                            0 => 'change',
                                        ],
                                    7 => 'route/lang/applications',
                                    8 => 'Applications\\Controller\\Manage',
                                    'Entity/Application' =>
                                        [
                                            '__ALL__' => 'Applications/Access',
                                        ],
                                    9 => 'route/lang/settings',
                                    10 => 'Settings\\Controller\\Index',
                                ],
                            'deny' =>
                                [
                                    0 => 'route/auth-provider',
                                    1 => 'route/auth-extern',
                                    2 => 'route/lang/forgot-password',
                                    3 => 'route/lang/goto-reset-password',
                                    4 => 'route/lang/register',
                                    5 => 'route/lang/register-confirmation',
                                ],
                        ],
                    'recruiter' =>
                        [
                            'allow' =>
                                [
                                    0 => 'route/lang/my-groups',
                                    1 => 'route/lang/cvs',
                                    2 => 'navigation/resume-recruiter',
                                    'Entity/Cv' =>
                                        [
                                            'view' => 'Cv/MayView',
                                            'edit' => 'Cv/MayChange',
                                        ],
                                    3 => 'Jobs',
                                    4 => 'JobList',
                                    'Jobs/Manage' =>
                                        [
                                            0 => 'delete',
                                            1 => 'edit',
                                            2 => 'deactivate',
                                            3 => 'completion',
                                            4 => 'deactivate',
                                            5 => 'template',
                                            'new' => 'Jobs/Create',
                                            6 => 'history',
                                            7 => 'channel-list',
                                        ],
                                    5 => 'JobboardRecruiter',
                                    6 => 'route/lang/jobs/manage',
                                    7 => 'route/lang/jobs/template',
                                    'Entity/Jobs/Job' =>
                                        [
                                            'edit' => 'Jobs/Write',
                                        ],
                                    8 => 'route/lang/organizations',
                                    9 => 'Organizations/InviteEmployee',
                                    'Entity/Organization' =>
                                        [
                                            'edit' => 'Organizations/Write',
                                        ],
                                ],
                            'deny' =>
                                [
                                    0 => 'route/lang/my-cv',
                                    1 => 'navigation/resume-user',
                                    2 => 'Cv/Status',
                                    3 => 'Jobboard',
                                    4 => 'route/lang/jobs/approval',
                                ],
                        ],
                    'applicant' =>
                        [
                            'allow' =>
                                [
                                    0 => 'Jobboard',
                                ],
                        ],
                ],
            'assertions' =>
                [
                    'factories' =>
                        [
                            'Core\\Acl\\FileAccessAssertion' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                        ],
                    'aliases' =>
                        [
                            'Core/FileAccess' => 'Core\\Acl\\FileAccessAssertion',
                        ],
                    'invokables' =>
                        [
                            'Cv/MayView' => 'Cv\\Acl\\Assertion\\MayViewCv',
                            'Cv/MayChange' => 'Cv\\Acl\\Assertion\\MayChangeCv',
                            'Applications/Access' => 'Applications\\Acl\\ApplicationAccessAssertion',
                            'Jobs/Write' => 'Jobs\\Acl\\WriteAssertion',
                            'Jobs/Create' => 'Jobs\\Acl\\CreateAssertion',
                            'Organizations/Write' => 'Organizations\\Acl\\Assertion\\WriteAssertion',
                        ],
                ],
            'roles' =>
                [
                    0 => 'guest',
                    'user' => 'guest',
                    'recruiter' => 'user',
                    'admin' => 'recruiter',
                    'employee_recruiter' => 'recruiter',
                ],
            'public_roles' =>
                [
                    0 => 'user',
                    1 => 'recruiter',
                ],
        ],
    'translator' =>
        [
            'locale' => 'en_EN',
            'translation_file_patterns' =>
                [
                    0 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Core/config/../language',
                            'pattern' => '%s.mo',
                        ],
                    1 =>
                        [
                            'type' => 'phparray',
                            'base_dir' => '/var/www/yawik/module/Core/config/../language',
                            'pattern' => 'Zend_Validate.%s.php',
                        ],
                    2 =>
                        [
                            'type' => 'phparray',
                            'base_dir' => '/var/www/yawik/module/Core/config/../language',
                            'pattern' => 'Zend_Captcha.%s.php',
                        ],
                    3 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Auth/config/../language',
                            'pattern' => '%s.mo',
                        ],
                    4 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Cv/config/../language',
                            'pattern' => '%s.mo',
                        ],
                    5 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Applications/config/../language',
                            'pattern' => '%s.mo',
                        ],
                    6 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Jobs/config/../language',
                            'pattern' => '%s.mo',
                        ],
                    7 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Settings/config/../language',
                            'pattern' => '%s.mo',
                        ],
                    8 =>
                        [
                            'type' => 'gettext',
                            'base_dir' => '/var/www/yawik/module/Organizations/config/../language',
                            'pattern' => '%s.mo',
                        ],
                ],
        ],
    'navigation' =>
        [
            'default' =>
                [
                    'home' =>
                        [
                            'label' => 'Home',
                            'route' => 'lang',
                            'visible' => false,
                        ],
                    'admin' =>
                        [
                            'label ' => 'Admin',
                            'route' => 'lang/admin',
                            'resource' => 'route/lang/admin',
                            'order' => 200,
                            'pages' =>
                                [
                                    'users' =>
                                        [
                                            'label' => 'Users',
                                            'route' => 'lang/user-list',
                                            'order' => '100',
                                            'resource' => 'Users',
                                        ],
                                    'jobs' =>
                                        [
                                            'label' => 'Jobs',
                                            'route' => 'lang/admin/jobs',
                                            'query' =>
                                                [
                                                    'clear' => '1',
                                                ],
                                            'active_on' =>
                                                [
                                                    0 => 'lang/jobs/approval',
                                                ],
                                        ],
                                    'jobs-categories' =>
                                        [
                                            'label' => 'Jobs categories',
                                            'route' => 'lang/admin/jobs-categories',
                                        ],
                                ],
                        ],
                    'resume-recruiter' =>
                        [
                            'label' => 'Talent-Pool',
                            'route' => 'lang/cvs',
                            'active_on' =>
                                [
                                    0 => 'lang/cvs/edit',
                                    1 => 'lang/cvs/view',
                                ],
                            'resource' => 'navigation/resume-recruiter',
                            'order' => 10,
                            'query' =>
                                [
                                    'clear' => '1',
                                ],
                            'pages' =>
                                [
                                    'list' =>
                                        [
                                            'label' => 'Overview',
                                            'route' => 'lang/cvs',
                                        ],
                                    'create' =>
                                        [
                                            'label' => 'Create resume',
                                            'route' => 'lang/cvs/create',
                                        ],
                                ],
                        ],
                    'resume-user' =>
                        [
                            'label' => 'Resume',
                            'route' => 'lang/my-cv',
                            'resource' => 'navigation/resume-user',
                            'order' => 10,
                        ],
                    'apply' =>
                        [
                            'label' => 'Applications',
                            'route' => 'lang/applications',
                            'order' => 20,
                            'resource' => 'route/lang/applications',
                            'query' =>
                                [
                                    'clear' => '1',
                                ],
                            'pages' =>
                                [
                                    'list' =>
                                        [
                                            'label' => 'Overview',
                                            'route' => 'lang/applications',
                                        ],
                                ],
                        ],
                    'jobboard' =>
                        [
                            'label' => 'Jobboard',
                            'route' => 'lang/jobboard',
                            'order' => '30',
                            'resource' => 'Jobboard',
                        ],
                    'jobs' =>
                        [
                            'label' => 'Jobs',
                            'route' => 'lang/jobs',
                            'order' => '30',
                            'resource' => 'Jobs',
                            'pages' =>
                                [
                                    'list' =>
                                        [
                                            'label' => 'Overview',
                                            'route' => 'lang/jobs',
                                            'resource' => 'JobList',
                                        ],
                                    'new' =>
                                        [
                                            'label' => 'Create job',
                                            'route' => 'lang/jobs/manage',
                                            'resource' => 'route/lang/jobs/manage',
                                            'params' =>
                                                [
                                                    'action' => 'edit',
                                                ],
                                            'id' => 'Jobs/new',
                                        ],
                                    'edit' =>
                                        [
                                            'label' => 'Edit job',
                                            'resource' => 'route/lang/jobs/manage',
                                            'uri' => '#',
                                            'visible' => false,
                                            'id' => 'Jobs/edit',
                                        ],
                                ],
                        ],
                    'settings' =>
                        [
                            'label' => 'Settings',
                            'route' => 'lang/settings',
                            'resource' => 'route/lang/settings',
                            'order' => 100,
                            'params' =>
                                [
                                    'module' => null,
                                ],
                        ],
                    'organizations' =>
                        [
                            'label' => 'Organizations',
                            'route' => 'lang/organizations',
                            'order' => 65,
                            'resource' => 'route/lang/organizations',
                            'pages' =>
                                [
                                    'list' =>
                                        [
                                            'label' => 'Overview',
                                            'route' => 'lang/organizations',
                                        ],
                                    'edit' =>
                                        [
                                            'label' => 'Insert',
                                            'route' => 'lang/organizations/edit',
                                        ],
                                ],
                        ],
                ],
        ],
    'view_helper_config' =>
        [
            'flashmessenger' =>
                [
                    'message_open_format' => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
                    'message_separator_string' => '</li><li>',
                    'message_close_string' => '</li></ul></div>',
                ],
            'form_editor' =>
                [
                    'light' =>
                        [
                            'toolbar' => 'undo redo | formatselect | alignleft aligncenter alignright ',
                            'block_formats' => 'Job title=h1;Subtitle=h2',
                        ],
                ],
            'headscript' =>
                [
                    'lang/applications' =>
                        [
                            0 => 'modules/Core/js/jquery.barrating.min.js',
                        ],
                ],
        ],
    'form_elements' =>
        [
            'invokables' =>
                [
                    'DefaultButtonsFieldset' => '\\Core\\Form\\DefaultButtonsFieldset',
                    'FormSubmitButtonsFieldset' => '\\Core\\Form\\FormSubmitButtonsFieldset',
                    'SummaryFormButtonsFieldset' => 'Core\\Form\\SummaryFormButtonsFieldset',
                    'Checkbox' => 'Core\\Form\\Element\\Checkbox',
                    'infoCheckBox' => 'Core\\Form\\Element\\InfoCheckbox',
                    'Core/ListFilterButtons' => '\\Core\\Form\\ListFilterButtonsFieldset',
                    'Core/Datepicker' => 'Core\\Form\\Element\\DatePicker',
                    'Core/FileUpload' => '\\Core\\Form\\Element\\FileUpload',
                    'Core\\FileCollection' => 'Core\\Form\\FileCollection',
                    'Core/LocalizationSettingsFieldset' => 'Core\\Form\\LocalizationSettingsFieldset',
                    'Core/RatingFieldset' => 'Core\\Form\\RatingFieldset',
                    'Core/Rating' => 'Core\\Form\\Element\\Rating',
                    'Core/PermissionsFieldset' => 'Core\\Form\\PermissionsFieldset',
                    'Core/PermissionsCollection' => 'Core\\Form\\PermissionsCollection',
                    'Location' => 'Laminas\\Form\\Element\\Text',
                    'Core/Spinner-Submit' => 'Core\\Form\\Element\\SpinnerSubmit',
                    'ToggleButton' => 'Core\\Form\\Element\\ToggleButton',
                    'TextEditor' => 'Core\\Form\\Element\\Editor',
                    'TextEditorLight' => 'Core\\Form\\Element\\EditorLight',
                    'Core/Container' => 'Core\\Form\\Container',
                    'Core/Tree/Management' => 'Core\\Form\\Tree\\ManagementForm',
                    'Core/Tree/ManagementFieldset' => 'Core\\Form\\Tree\\ManagementFieldset',
                    'Core/Tree/AddItemFieldset' => 'Core\\Form\\Tree\\AddItemFieldset',
                    'Core/Search' => 'Core\\Form\\SearchForm',
                    'Auth/Login' => 'Auth\\Form\\Login',
                    'user-profile' => 'Auth\\Form\\UserProfile',
                    'Auth/UserPasswordFieldset' => 'Auth\\Form\\UserPasswordFieldset',
                    'Auth/UserBase' => 'Auth\\Form\\UserBase',
                    'Auth/UserBaseFieldset' => 'Auth\\Form\\UserBaseFieldset',
                    'Auth/Group' => 'Auth\\Form\\Group',
                    'Auth/Group/Data' => 'Auth\\Form\\GroupFieldset',
                    'Auth/Group/Users' => 'Auth\\Form\\GroupUsersCollection',
                    'Auth/Group/User' => 'Auth\\Form\\GroupUserElement',
                    'Auth/SocialProfilesButton' => 'Auth\\Form\\Element\\SocialProfilesButton',
                    'Auth/SocialProfiles' => 'Auth\\Form\\SocialProfiles',
                    'Auth/UserInfoContainer' => 'Auth\\Form\\UserInfoContainer',
                    'Auth/UserInfo' => 'Auth\\Form\\UserInfo',
                    'Auth/UserProfileContainer' => 'Auth\\Form\\UserProfileContainer',
                    'Auth/UserStatusContainer' => 'Auth\\Form\\UserStatusContainer',
                    'Auth/UserStatus' => 'Auth\\Form\\UserStatus',
                    'CvContainer' => '\\Cv\\Form\\CvContainer',
                    'EducationFieldset' => '\\Cv\\Form\\EducationFieldset',
                    'EmploymentFieldset' => '\\Cv\\Form\\EmploymentFieldset',
                    'SkillFieldset' => '\\Cv\\Form\\SkillFieldset',
                    'LanguageSkillFieldset' => '\\Cv\\Form\\LanguageFieldset',
                    'CvEmploymentForm' => '\\Cv\\Form\\EmploymentForm',
                    'CvEducationForm' => '\\Cv\\Form\\EducationForm',
                    'CvSkillForm' => '\\Cv\\Form\\SkillForm',
                    'Cv/PreferredJobForm' => 'Cv\\Form\\PreferredJobForm',
                    'Cv/LanguageSkillForm' => '\\Cv\\Form\\LanguageSkillForm',
                    'Cv/LanguageSkillFieldset' => '\\Cv\\Form\\LanguageSkillFieldset',
                    'Cv/NativeLanguageForm' => '\\Cv\\Form\\NativeLanguageForm',
                    'Cv/NativeLanguageFieldset' => '\\Cv\\Form\\NativeLanguageFieldset',
                    'Cv/SearchForm' => '\\Cv\\Form\\SearchForm',
                    'Applications/Mail' => 'Applications\\Form\\Mail',
                    'Applications/BaseFieldset' => 'Applications\\Form\\BaseFieldset',
                    'Applications/SettingsFieldset' => 'Applications\\Form\\SettingsFieldset',
                    'Applications/CommentForm' => 'Applications\\Form\\CommentForm',
                    'Applications/CommentFieldset' => 'Applications\\Form\\CommentFieldset',
                    'Applications/Apply' => 'Applications\\Form\\Apply',
                    'Applications/Contact' => 'Applications\\Form\\ContactContainer',
                    'Applications/Base' => 'Applications\\Form\\Base',
                    'Applications/Facts' => 'Applications\\Form\\Facts',
                    'Applications/FactsFieldset' => 'Applications\\Form\\FactsFieldset',
                    'Applications/Attributes' => 'Applications\\Form\\Attributes',
                    'href' => 'Applications\\Form\\Element\\Ref',
                    'Jobs/Base' => 'Jobs\\Form\\Base',
                    'Jobs/Employers' => 'Jobs\\Form\\JobEmployers',
                    'Jobs/JobEmployersFieldset' => 'Jobs\\Form\\JobEmployersFieldset',
                    'Jobs/Description' => 'Jobs\\Form\\JobDescription',
                    'Jobs/JobDescriptionFieldset' => 'Jobs\\Form\\JobDescriptionFieldset',
                    'Jobs/ApplyId' => 'Jobs\\Form\\ApplyIdentifierElement',
                    'Jobs/ImportFieldset' => 'Jobs\\Form\\ImportFieldset',
                    'Jobs/ListFilterPersonalFieldset' => 'Jobs\\Form\\ListFilterPersonalFieldset',
                    'Jobs/ListFilterAdminFieldset' => 'Jobs\\Form\\ListFilterAdminFieldset',
                    'Jobs/JobDescriptionDescription' => 'Jobs\\Form\\JobDescriptionDescription',
                    'Jobs/JobDescriptionBenefits' => 'Jobs\\Form\\JobDescriptionBenefits',
                    'Jobs/JobDescriptionRequirements' => 'Jobs\\Form\\JobDescriptionRequirements',
                    'Jobs/TemplateLabelRequirements' => 'Jobs\\Form\\TemplateLabelRequirements',
                    'Jobs/TemplateLabelQualifications' => 'Jobs\\Form\\TemplateLabelQualifications',
                    'Jobs/TemplateLabelBenefits' => 'Jobs\\Form\\TemplateLabelBenefits',
                    'Jobs/JobDescriptionQualifications' => 'Jobs\\Form\\JobDescriptionQualifications',
                    'Jobs/JobDescriptionTitle' => 'Jobs\\Form\\JobDescriptionTitle',
                    'Jobs/JobDescriptionHtml' => 'Jobs\\Form\\JobDescriptionHtml',
                    'Jobs/Description/Template' => 'Jobs\\Form\\JobDescriptionTemplate',
                    'Jobs/Preview' => 'Jobs\\Form\\Preview',
                    'Jobs/PreviewFieldset' => 'Jobs\\Form\\PreviewFieldset',
                    'Jobs/PreviewLink' => 'Jobs\\Form\\PreviewLink',
                    'Jobs/CompanyName' => 'Jobs\\Form\\CompanyName',
                    'Jobs/CompanyNameElement' => 'Jobs\\Form\\CompanyNameElement',
                    'Jobs/Multipost' => 'Jobs\\Form\\Multipost',
                    'Jobs/MultipostFieldset' => 'Jobs\\Form\\MultipostFieldset',
                    'Jobs/MultipostButtonFieldset' => 'Jobs\\Form\\MultipostButtonFieldset',
                    'Jobs/AtsMode' => 'Jobs\\Form\\AtsMode',
                    'Jobs/AtsModeFieldset' => 'Jobs\\Form\\AtsModeFieldset',
                    'Jobs/AdminSearch' => 'Jobs\\Form\\AdminSearchFormElementsFieldset',
                    'Jobs/ListFilter' => 'Jobs\\Form\\ListFilter',
                    'Jobs/ListFilterLocation' => 'Jobs\\Form\\ListFilterLocation',
                    'Jobs/ListFilterPersonal' => 'Jobs\\Form\\ListFilterPersonal',
                    'Jobs/ListFilterAdmin' => 'Jobs\\Form\\ListFilterAdmin',
                    'Jobs/StatusSelect' => 'Jobs\\Form\\Element\\StatusSelect',
                    'Jobs/AdminJobEdit' => 'Jobs\\Form\\AdminJobEdit',
                    'Jobs/AdminCategories' => 'Jobs\\Form\\CategoriesContainer',
                    'Jobs/Classifications' => 'Jobs\\Form\\ClassificationsForm',
                    'Jobs/ClassificationsFieldset' => 'Jobs\\Form\\ClassificationsFieldset',
                    'Jobs/CustomerNote' => 'Jobs\\Form\\CustomerNote',
                    'Jobs/CustomerNoteFieldset' => 'Jobs\\Form\\CustomerNoteFieldset',
                    'Jobs/ManagerSelect' => 'Jobs\\Form\\Element\\ManagerSelect',
                    'SimpleLocationSelect' => 'Geo\\Form\\GeoSelectSimple',
                    'Organizations/Form' => 'Organizations\\Form\\Organizations',
                    'Organizations/OrganizationsContactForm' => 'Organizations\\Form\\OrganizationsContactForm',
                    'Organizations/OrganizationsNameForm' => 'Organizations\\Form\\OrganizationsNameForm',
                    'Organizations/OrganizationsDescriptionForm' => 'Organizations\\Form\\OrganizationsDescriptionForm',
                    'Organizations/OrganizationsContactFieldset' => 'Organizations\\Form\\OrganizationsContactFieldset',
                    'Organizations/OrganizationsDescriptionFieldset' => 'Organizations\\Form\\OrganizationsDescriptionFieldset',
                    'Organizations/EmployeesContainer' => 'Organizations\\Form\\EmployeesContainer',
                    'Organizations/Employees' => 'Organizations\\Form\\Employees',
                    'Organizations/InviteEmployeeBar' => 'Organizations\\Form\\Element\\InviteEmployeeBar',
                    'Organizations/Employee' => 'Organizations\\Form\\Element\\Employee',
                    'Organizations/WorkflowSettings' => 'Organizations\\Form\\WorkflowSettings',
                    'Organizations/WorkflowSettingsFieldset' => 'Organizations\\Form\\WorkflowSettingsFieldset',
                ],
            'factories' =>
                [
                    'Core/Tree/Select' => 'Core\\Factory\\Form\\Tree\\SelectFactory',
                    'Auth/RoleSelect' => 'Auth\\Factory\\Form\\RoleSelectFactory',
                    'Auth/UserInfoFieldset' => 'Auth\\Factory\\Form\\UserInfoFieldsetFactory',
                    'Auth/UserStatusFieldset' => 'Auth\\Factory\\Form\\UserStatusFieldsetFactory',
                    'Auth/SocialProfilesFieldset' => 'Auth\\Factory\\Form\\SocialProfilesFieldsetFactory',
                    'Auth/UserImage' => 'Auth\\Form\\UserImageFactory',
                    'Auth\\Form\\Login' => 'Auth\\Factory\\Form\\LoginFactory',
                    'Auth\\Form\\Register' => 'Auth\\Factory\\Form\\RegisterFactory',
                    'user-password' =>
                        [
                            0 => 'Auth\\Form\\UserPassword',
                            1 => 'factory',
                        ],
                    'CvEmploymentCollection' => '\\Cv\\Factory\\Form\\EmploymentCollectionFactory',
                    'CvEducationCollection' => '\\Cv\\Factory\\Form\\EducationCollectionFactory',
                    'CvSkillCollection' => '\\Cv\\Factory\\Form\\SkillCollectionFactory',
                    'Cv/LanguageSkillCollection' => '\\Cv\\Factory\\Form\\LanguageSkillCollectionFactory',
                    'CvContactImage' => '\\Cv\\Factory\\Form\\CvContactImageFactory',
                    'Cv\\Form\\PreferredJobFieldset' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Cv/SearchFormFieldset' => '\\Cv\\Factory\\Form\\SearchFormFieldsetFactory',
                    'Cv/Attachments' => '\\Cv\\Factory\\Form\\AttachmentsFormFactory',
                    'Applications/ContactImage' => 'Applications\\Factory\\Form\\ContactImageFactory',
                    'Applications/Attachments' => 'Applications\\Factory\\Form\\AttachmentsFactory',
                    'Applications\\Form\\ApplicationsFilter' => 'Laminas\\ServiceManager\\Factory\\InvokableFactory',
                    'Applications\\Form\\Element\\StatusSelect' => 'Applications\\Factory\\Form\\StatusSelectFactory',
                    'Applications\\Form\\Element\\JobSelect' => 'Applications\\Factory\\Form\\JobSelectFactory',
                    'Jobs/Job' => 'Jobs\\Factory\\Form\\JobFactory',
                    'Jobs/BaseFieldset' => 'Jobs\\Factory\\Form\\BaseFieldsetFactory',
                    'Jobs/ListFilterLocationFieldset' => 'Jobs\\Factory\\Form\\ListFilterLocationFieldsetFactory',
                    'Jobs/JobboardSearch' => 'Jobs\\Factory\\Form\\JobboardSearchFactory',
                    'Jobs/CompanyNameFieldset' => 'Jobs\\Factory\\Form\\CompanyNameFieldsetFactory',
                    'Jobs/HiringOrganizationSelect' => 'Jobs\\Factory\\Form\\HiringOrganizationSelectFactory',
                    'Jobs/ActiveOrganizationSelect' => 'Jobs\\Factory\\Form\\ActiveOrganizationSelectFactory',
                    'Jobs/MultipostingSelect' => 'Jobs\\Factory\\Form\\MultipostingMultiCheckboxFactory',
                    'Jobs/Import' => 'Jobs\\Factory\\Form\\ImportFactory',
                    'Settings/Form' =>
                        [
                            0 => 'Settings\\Form\\AbstractSettingsForm',
                            1 => 'factory',
                        ],
                    'Settings/DisableElementsCapableFormSettingsFieldset' =>
                        [
                            0 => 'Settings\\Form\\DisableElementsCapableFormSettingsFieldset',
                            1 => 'factory',
                        ],
                    'Settings/Fieldset' => 'Settings\\Form\\Factory\\SettingsFieldsetFactory',
                    'LocationSelect' => 'Geo\\Factory\\Form\\GeoSelectFactory',
                    'Organizations/OrganizationsNameFieldset' => 'Organizations\\Factory\\Form\\OrganizationsNameFieldsetFactory',
                    'Organizations/Image' => 'Organizations\\Form\\LogoImageFactory',
                    'Organizations/EmployeesFieldset' => 'Organizations\\Factory\\Form\\EmployeesFieldsetFactory',
                    'Organizations/EmployeeFieldset' => 'Organizations\\Factory\\Form\\EmployeeFieldsetFactory',
                ],
            'initializers' =>
                [
                    0 => '\\Core\\Form\\Service\\InjectHeadscriptInitializer',
                    1 => '\\Core\\Form\\Service\\Initializer',
                ],
            'aliases' =>
                [
                    'submitField' => 'FormSubmitButtonsFieldset',
                ],
        ],
    'paginator_manager' =>
        [
            'abstract_factories' =>
                [
                    0 => '\\Core\\Factory\\Paginator\\RepositoryAbstractFactory',
                ],
            'factories' =>
                [
                    'Cv/Paginator' => 'Cv\\Paginator\\PaginatorFactory',
                    'Applications\\Paginator\\JobSelectPaginator' => 'Applications\\Factory\\Paginator\\JobSelectPaginatorFactory',
                    'Jobs/Job' => 'Jobs\\Paginator\\JobsPaginatorFactory',
                    'Jobs/Admin' => 'Jobs\\Paginator\\JobsAdminPaginatorFactory',
                    'Jobs\\Paginator\\ActiveOrganizations' => 'Jobs\\Factory\\Paginator\\ActiveOrganizationsPaginatorFactory',
                ],
            'invokables' =>
                [
                ],
            'aliases' =>
                [
                    'Jobs/Board' => 'Jobs/Job',
                ],
        ],
    'mails_config' =>
        [
            'from' =>
                [
                    'email' => 'no-reply@host.tld',
                    'name' => 'YAWIK',
                ],
        ],
    'event_manager' =>
        [
            'Core/AdminController/Events' =>
                [
                    'service' => 'Core/EventManager',
                    'event' => '\\Core\\Controller\\AdminControllerEvent',
                    'listeners' =>
                        [
                            'Jobs/Listener/AdminWidgetProvider' => 'DASHBOARD',
                        ],
                ],
            'Core/CreatePaginator/Events' =>
                [
                    'service' => 'Core/EventManager',
                    'event' => '\\Core\\Listener\\Events\\CreatePaginatorEvent',
                ],
            'Core/ViewSnippets/Events' =>
                [
                    'service' => 'Core/EventManager',
                ],
            'Core/Ajax/Events' =>
                [
                    'service' => 'Core/EventManager',
                    'event' => 'Core\\Listener\\Events\\AjaxEvent',
                    'listeners' =>
                        [
                            'Applications\\Listener\\JobSelectValues' =>
                                [
                                    0 => 'applications.job-select',
                                    1 => true,
                                ],
                            'Jobs\\Listener\\DeleteJob' =>
                                [
                                    0 => 'jobs.delete',
                                    1 => true,
                                ],
                            'Jobs\\Listener\\GetOrganizationManagers' =>
                                [
                                    0 => 'jobs.manager-select',
                                    1 => true,
                                ],
                            'Jobs\\Listener\\LoadActiveOrganizations' =>
                                [
                                    0 => 'jobs.admin.activeorganizations',
                                    1 => true,
                                ],
                            'Geo\\Listener\\AjaxQuery' =>
                                [
                                    0 => 'geo',
                                    1 => true,
                                ],
                        ],
                ],
            'Core/File/Events' =>
                [
                    'service' => 'Core/EventManager',
                    'event' => 'Core\\Listener\\Events\\FileEvent',
                    'listeners' =>
                        [
                            'Core\\Listener\\DeleteImageSetListener' =>
                                [
                                    0 => 'delete',
                                    1 => -1000,
                                ],
                        ],
                ],
            'Auth/Events' =>
                [
                    'service' => 'Core/EventManager',
                    'event' => 'Auth\\Listener\\Events\\AuthEvent',
                    'listeners' =>
                        [
                            'Auth\\Listener\\MailForgotPassword' =>
                                [
                                    0 => 'auth.newpassword',
                                    1 => 10,
                                    2 => true,
                                ],
                            'Auth\\Listener\\SendRegistrationNotifications' =>
                                [
                                    0 =>
                                        [
                                            0 => 'auth.user-registered',
                                            1 => 'auth.user-confirmed',
                                        ],
                                    1 => true,
                                ],
                        ],
                ],
            'Applications/Events' =>
                [
                    'event' => '\\Applications\\Listener\\Events\\ApplicationEvent',
                    'service' => 'Core/EventManager',
                    'listeners' =>
                        [
                            'Applications/Listener/ApplicationCreated' =>
                                [
                                    0 => 'application.post.create',
                                    1 => true,
                                ],
                            'Applications/Listener/ApplicationStatusChangePre' =>
                                [
                                    0 => 'application.status.change',
                                    1 => true,
                                    2 => 100,
                                    3 => 'prepareFormData',
                                ],
                            'Applications/Listener/ApplicationStatusChangePost' =>
                                [
                                    0 => 'application.status.change',
                                    1 => true,
                                    2 => -10,
                                    3 => 'sendMail',
                                ],
                        ],
                ],
            'Auth/Dependency/Manager/Events' =>
                [
                    'listeners' =>
                        [
                            'Applications\\Auth\\Dependency\\ListListener' =>
                                [
                                    0 => 'getLists',
                                    1 => true,
                                ],
                            'Jobs\\Auth\\Dependency\\ListListener' =>
                                [
                                    0 => 'getLists',
                                    1 => true,
                                ],
                            'Organizations\\Auth\\Dependency\\ListListener' =>
                                [
                                    0 => 'getLists',
                                    1 => true,
                                    2 => 10,
                                ],
                            'Organizations\\Auth\\Dependency\\EmployeeListListener' =>
                                [
                                    0 => 'getLists',
                                    1 => true,
                                    2 => 11,
                                ],
                        ],
                ],
            'Jobs/Events' =>
                [
                    'service' => 'Core/EventManager',
                    'event' => '\\Jobs\\Listener\\Events\\JobEvent',
                ],
            'Jobs/JobContainer/Events' =>
                [
                    'event' => '\\Core\\Form\\Event\\FormEvent',
                ],
        ],
    'hybridauth' =>
        [
            'Facebook' =>
                [
                    'enabled' => true,
                    'keys' =>
                        [
                            'id' => '496979067308911',
                            'secret' => '8d331be661d1aff53d09a5477189a3e2',
                        ],
                    'scope' => 'email, user_about_me, user_birthday, user_hometown, user_work_history, user_education_history',
                    'display' => 'popup',
                ],
            'LinkedIn' =>
                [
                    'enabled' => true,
                    'keys' =>
                        [
                            'key' => '',
                            'secret' => 'ncPdikjqfiIsCeYu',
                            'id' => '81czo5vru04x1s',
                        ],
                    'scope' => 'r_basicprofile,r_emailaddress',
                ],
            'XING' =>
                [
                    'enabled' => true,
                    'wrapper' =>
                        [
                            'class' => 'Hybrid_Providers_XING',
                            'path' => '/var/www/yawik/module/Auth/config/module.config.php',
                        ],
                    'keys' =>
                        [
                            'key' => '557dc2156e6a9f48af71',
                            'secret' => '189e6427dd09d97fa21537ce3dd12236782811e6',
                        ],
                    'scope' => '',
                ],
            'Github' =>
                [
                    'enabled' => true,
                    'wrapper' =>
                        [
                            'class' => 'Hybrid_Providers_Github',
                            'path' => '/var/www/yawik/module/Auth/config/module.config.php',
                        ],
                    'keys' =>
                        [
                            'key' => '',
                            'secret' => '###Your GitHub Secret###',
                            'id' => '###Your GitHub AppID ###',
                        ],
                    'scope' => '',
                ],
            'Google' =>
                [
                    'enabled' => true,
                    'keys' =>
                        [
                            'id' => '###Your Google Client-ID ###',
                            'secret' => '###Your GitHub Secret###',
                        ],
                    'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
                    'access_type' => 'offline',
                    'approval_prompt' => 'force',
                ],
        ],
    'mails' =>
        [
            'invokables' =>
                [
                    'Auth\\Mail\\RegisterConfirmation' => 'Auth\\Mail\\RegisterConfirmation',
                    'Applications/StatusChange' => 'Applications\\Mail\\StatusChange',
                    'Applications/CarbonCopy' => 'Applications\\Mail\\ApplicationCarbonCopy',
                ],
            'factories' =>
                [
                    'Applications/NewApplication' => 'Applications\\Factory\\Mail\\NewApplicationFactory',
                    'Applications\\Mail\\Confirmation' => 'Applications\\Factory\\Mail\\ConfirmationFactory',
                    'Applications/Forward' =>
                        [
                            0 => 'Applications\\Mail\\Forward',
                            1 => 'factory',
                        ],
                    'Organizations/InviteEmployee' => 'Organizations\\Mail\\EmployeeInvitationFactory',
                ],
            'aliases' =>
                [
                    'Applications/Confirmation' => 'Applications\\Mail\\Confirmation',
                ],
            'develop' =>
                [
                    'override_recipient' => 'test.yawik@gmail.com',
                ],
        ],
    'input_filters' =>
        [
            'aliases' =>
                [
                    'Cv/Employment' => 'Cv\\Form\\InputFilter\\Employment',
                    'Cv/Education' => 'Cv\\Form\\InputFilter\\Education',
                    'Jobs/Location/Edit' => 'Jobs\\Form\\InputFilter\\JobLocationEdit',
                ],
            'invokables' =>
                [
                    'Cv/Employment' => 'Cv\\Form\\InputFilter\\Employment',
                    'Cv/Education' => 'Cv\\Form\\InputFilter\\Education',
                    'Jobs/Location/New' => 'Jobs\\Form\\InputFilter\\JobLocationNew',
                    'Jobs\\Form\\InputFilter\\JobLocationEdit' => 'Jobs\\Form\\InputFilter\\JobLocationEdit',
                    'Jobs/Company' => 'Jobs\\Form\\InputFilter\\CompanyName',
                ],
            'factories' =>
                [
                    'Jobs/AtsMode' => 'Jobs\\Factory\\Form\\InputFilter\\AtsModeFactory',
                ],
        ],
    'Applications' =>
        [
            'dashboard' =>
                [
                    'enabled' => true,
                    'widgets' =>
                        [
                            'recentApplications' =>
                                [
                                    'controller' => 'Applications\\Controller\\Index',
                                ],
                        ],
                ],
            'settings' =>
                [
                    'entity' => '\\Applications\\Entity\\Settings',
                    'navigation_order' => 1,
                    'navigation_label' => 'E-Mail Templates',
                    'navigation_class' => 'yk-icon yk-icon-envelope',
                ],
        ],
    'form_elements_config' =>
        [
            'Applications/Apply' =>
                [
                    'disable_elements' =>
                        [
                            0 => 'facts',
                        ],
                ],
            'file_upload_factories' =>
                [
                    'organization_logo_image' =>
                        [
                            'hydrator' => 'Organizations/Logo',
                        ],
                ],
        ],
    'Jobs' => [
        'dashboard' =>
            [
                'enabled' => true,
                'widgets' =>
                    [
                        'recentJobs' =>
                            [
                                'controller' => 'Jobs/Index',
                                'params' =>
                                    [
                                        'type' => 'recent',
                                    ],
                            ],
                    ],
            ],
    ],
    'Organizations' =>
        [
            'form' =>
                [
                ],
            'dashboard' =>
                [
                    'enabled' => false,
                    'widgets' =>
                        [
                        ],
                ],
        ],
    'Core\\Listener\\DeleteImageSetListener' =>
        [
            'Organizations\\Entity\\OrganizationImage' =>
                [
                    'repository' => 'Organizations',
                    'property' => 'images',
                ],
        ],
    'core_options' =>
        [
            'system_message_email' => 'install@example.com',
        ],
    'Auth' =>
        [
            'first_login' =>
                [
                    'role' => '%%role%%',
                    'auth_suffix' => '%%auth.suffix%%',
                ],
            'external_applications' =>
                [
                    '%%external.app.prefix%%' => '%%external.app.key%%',
                ],
        ],
];
