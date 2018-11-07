<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author bleek@cross-solution.de
 * @license   MIT
 */

namespace Applications\Form;

use Core\Form\Form;

/**
 * Formular for inviting or rejecting applicants
 */
class Mail extends Form
{

    /**
     * initialize mail forward form
     */
    public function init()
    {
        $this->setName('applicant-mail');

        
        $this
        ->add(
            array(
            'type' => 'hidden',
            'name' => 'applicationId',
            )
        )
        ->add(
            array(
            'type' => 'hidden',
            'name' => 'status',
            )
        )
        ->add(
            array(
            'name' => 'mailSubject',
            )
        )
        ->add(
            array(
            'type' => 'textarea',
            'name' => 'mailText'
            )
        );
    }
}
