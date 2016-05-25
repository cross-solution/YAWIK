<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

return [
    'navigation' => [
        'default' => [
            'admin' => [
                'label' => 'Admin',
                'pages' => [
                    'orders' => [
                        'label'    =>  /*@translate*/ 'Orders',
                        'route'    => 'lang/orders',
                        'order'    => '100',
                        'resource' => 'Orders/Navigation',
                    ],
                ]
            ],
        ]
    ]
];