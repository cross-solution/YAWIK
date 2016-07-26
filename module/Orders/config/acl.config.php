<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

return [ 'acl' => [

    'rules' => [
        'admin' => [
            'allow' => [
                'Orders/Navigation',
                'route/lang/orders',
            ],
        ],
    ],

]];