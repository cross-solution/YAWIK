<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'Orders\Entity' => 'annotation',
                ],
            ],
        ],
    ],


    'event_manager' => [

        'Jobs/Events' => [ 'listeners' => [
            'Orders/Listener/CreateJobOrder' => [ \Jobs\Listener\Events\JobEvent::EVENT_JOB_CREATED, true ],
        ]],

        'Jobs/JobContainer/Events' => [ 'listeners' => [
            'Orders\Form\Listener\InjectInvoiceAddressInJobContainer' => \Core\Form\Event\FormEvent::EVENT_INIT,
            '\Orders\Form\Listener\ValidateJobInvoiceAddress' => 'ValidateJob',
        ]],

        'Orders/Form/InvoiceAddress/Events' => [
            'event' => '\Core\Form\Event\FormEvent',
            'listeners'=> [
                'Orders/Listener/BindInvoiceAddressEntity' => \Core\Form\Event\FormEvent::EVENT_SET_PARAM,
            ],
        ],
    ],

    'options' => [
        'Orders/Options/Module' => [
            'class' => '\Orders\Options\ModuleOptions',
        ],
    ],

    'Orders' => [
        'settings' => array(
            'entity' => '\Orders\Entity\SettingsContainer',
            //'navigation_order' => 1,
            'navigation_label' => /*@translate*/ "Orders",
            'navigation_class' => 'yk-icon fa-shopping-basket'
        ),
    ],

    'view_helper_config' => [
        'headscript' => [
            'lang/orders' => [
                [ \Zend\View\Helper\HeadScript::FILE, 'Orders/js/list.index.js', 'PREPEND' ],
                'js/bootstrap-dialog.min.js'
            ],
        ],
    ],
];
  