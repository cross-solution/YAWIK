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
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserProfileContainer extends Container
{
    public function init()
    {
        $this->setForms(array(
            'info' => 'Auth/UserInfoContainer',
            'base' => array(
                'type' => 'Auth/UserBase',
                'label' => /*@translate*/ 'General settings',
                'property' => true,
            ),
        ));
    }
}
