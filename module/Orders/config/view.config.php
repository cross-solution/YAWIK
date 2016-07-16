<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
$dir = realpath(__DIR__ . '/../view');

return [ 'view_manager' => [ 'template_map' => [

    'orders/list/index' => $dir . '/orders/list/index.phtml',
    'orders/list/index.ajax' => $dir . '/orders/list/index.ajax.phtml',
    'orders/list/view' => $dir . '/orders/list/view.phtml',

]]];