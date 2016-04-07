<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
return [

    'service_manager' => [
        'factories' => [
            'Orders/Listener/BindInvoiceAddressEntity' => 'Orders\Factory\Form\Listener\BindInvoiceAddressEntityFactory',
            'Orders/Listener/CreateJobOrder' => 'Orders\Factory\Listener\CreateJobOrderFactory',
            'Orders/Listener/AdminWidgetProvider' => 'Orders\Factory\Listener\AdminWidgetProviderFactory',
            'Orders/Entity/JobInvoiceAddress' => 'Orders\Factory\Entity\JobInvoiceAddressFactory',
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Orders/List' => 'Orders\Controller\ListController',
        ],
    ],

    'form_elements' => [
        'invokables' => [
            'Orders/InvoiceAddress' => 'Orders\Form\InvoiceAddress',
            'Orders/InvoiceAddressFieldset' => 'Orders\Form\InvoiceAddressFieldset',
            'Orders/InvoiceAddressSettingsFieldset' => '\Orders\Form\InvoiceAddressSettingsFieldset',
        ],
        'factories' => [
            'Orders/JobInvoiceAddress' => 'Orders\Factory\Form\JobInvoiceAddressFactory',
        ],
    ],

    'filters' => [
        'invokables' => [
            'PaginationQuery/Orders' => 'Orders\Repository\Filter\PaginationQuery',
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'formatInvoiceAddress' => 'Orders\View\Helper\FormatInvoiceAddress',
        ],
    ],
];