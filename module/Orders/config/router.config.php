<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
return [ 'router' => [ 'routes' => [ 'lang' => [ 'child_routes' => [

    'orders' => [
        'type' => 'Segment',
        'options' => [
            'route' => '/orders[/:action]',
            'defaults' => [
                'controller' => 'Orders/List',
                'action' => 'index'
            ],
        ],
        'may_terminate' => true,
    ],

]]]]];