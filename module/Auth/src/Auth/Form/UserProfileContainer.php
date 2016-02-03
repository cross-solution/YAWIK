<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        $this->setForms(
            array(
            'info' => 'Auth/UserInfoContainer',

            /*
             * commented, because the role select box on the users profile page was removed.
             */

            //            'base' => array(
            //                'type' => 'Auth/UserBase',
            //                'label' => /*@translate*/ 'General settings',
            //                'property' => true,
            //            ),
            )
        );
    }
}
