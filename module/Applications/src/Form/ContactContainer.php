<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/**  */
namespace Applications\Form;

use Auth\Form\UserInfoContainer;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ContactContainer extends UserInfoContainer
{
    /**
     * initialize contact container
     */
    public function init()
    {
        $this->setIsDisableCapable(false)
             ->setIsDisableElementsCapable(false);

        $this->setForms(
            array(
            'contact' => array(
                'type' => 'Auth/UserInfo',
                'property' => true,
            ),
            'image' => array(
                'type' => 'Applications/ContactImage',
                'property' => true,
                'use_files_array' => true,
            ),
            )
        );
    }
}
