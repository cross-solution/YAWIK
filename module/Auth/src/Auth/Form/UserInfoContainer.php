<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Auth\Form;

use Core\Form\Container;
use Zend\Form\FormInterface;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserInfoContainer extends Container
{
    protected $forms = array(
        'info' => array(
            'type' => 'Auth/UserInfo',
            'property' => true,
        ),
    );
      
}
