<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
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
