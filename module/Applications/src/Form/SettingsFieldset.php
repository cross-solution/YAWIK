<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Form;

use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Zend\Form\Element\Checkbox;
use Zend\Form\Fieldset;
use Zend\Stdlib\InitializableInterface;

//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsFieldset extends Fieldset
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new SettingsEntityHydrator());
        }
        return $this->hydrator;
    }

    public function setObject($object)
    {
        parent::setObject($object);
        /* @var $applyFormSettings \Settings\Form\DisableElementsCapableFormSettingsFieldset */
        $applyFormSettings = $this->get('applyFormSettings');
        $applyFormSettings->setObject($object->applyFormSettings);
        return $this;
    }

    /**
     * initialize settings form
     */
    public function init()
    {
        $this->setName('emails')
             ->setLabel(/* @translate */ 'E-Mail Notifications');
        
        $this->add(
            array('type' => Checkbox::class,
                'name' => 'mailAccess',
                'label' => 'foo',
                'options' => array('label' => /* @translate */ 'receive E-Mail alert',
                                   'long_label' => /* @translate */ 'if checked, you\'ll be informed by mail about new applications.'),
                )
        );
        $this->add(
            array('type' => 'Zend\Form\Element\Textarea',
                'name' => 'mailAccessText',
                'options' => array('label' => /* @translate */ 'Mailtext',
                                            'description' => /* @translate */ 'default text of the notification mail about new applications. The following variables can be used:<ul><li>##name## = your name</li><li>##title## = title of the job</li><li>##link## = Url of the application detail page</li></ul>'))
        );
        
        $this->add(
            array('type' => 'Zend\Form\Element\Checkbox',
                'name' => 'autoConfirmMail',
                'options' => array('label' => /* @translate */ 'confirm application immidiatly after submit',
                                   'long_label' => /* @translate */ 'if checked, an application is immediatly confirmed. If unchecked confirmation is the duty of the recruiter.'),
                )
        );
        $this->add(
            array('type' => 'Zend\Form\Element\Textarea',
                        'name' => 'mailConfirmationText',
                         'options' => array('label' => /* @translate */ 'Confirmation mail text',
                                            'description' => /* @translate */ 'default text of the acknowledgment of receipt mail to the applicant. The following variables can be used:<br><ul><li>##anrede_formell## = salutation. Includes gender, firstname and lastname.<li>##anrede_informell## = salutation. Includes fistname and lastname.</li><li>##job_title## = title of the jobs</li><li>##name## = name of the applicant.</li><li>##date## = date of recipt of the application.</li><li>##link## = Link to the application details</li></ul>' ))
        );
        
        $this->add(
            array('type' => 'Zend\Form\Element\Textarea',
                'name' => 'mailInvitationText',
                'options' => array('label' => /* @translate */ 'Invitation mail text',
                                    'description'=> /* @translate */ 'default text of the invitation mail to the applicant. You can use all variables of the acknowledgment of receipt mail. '
                                    ))
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Textarea',
                'name' => 'mailAcceptedText',
                'options' => [
                    'label' => /* @translate */ 'Accept mail text',
                    'description'=> /* @translate */ 'default text, when accepting an applicant. This mail is send to by a domain admin to the recruiter, who is responsible for the job posting.'
                ]
            ]
        );
        
        $this->add(
            [
                'type' => 'Zend\Form\Element\Textarea',
                'name' => 'mailRejectionText',
                'options' => [
                    'label' => /* @translate */ 'Rejection mail text',
                    'description' => /* @translate */ 'default text of the refusal of an application to the applicant. You can use all variables of the acknowledgment of receipt mail.'
                ]
            ]
        );
        
        $this->add(
            [
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'mailBCC',
                'options' => [
                    'label' => /* @translate */ 'get blind carbon copy of all own mails',
                    'long_label' => /* @translate */ 'if checked, you\'ll get a copy of all mails you send.',
                    'value_options' => [0, 1, true, false]
                ]
            ]
        );

        $this->add(
            array(
                'type' => 'Settings/DisableElementsCapableFormSettingsFieldset',
                'name' => 'applyFormSettings',
                'options' => array(
    
                )
            )
        );
    }
}
