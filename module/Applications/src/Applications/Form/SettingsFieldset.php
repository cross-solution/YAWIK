<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Applications\Form;

use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Zend\Form\Fieldset;
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

public function init()
    {
        $this->setName('emails')
             ->setLabel(/* @translate */ 'E-Mail Notifications');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailAccess',
        		'options' => array('label' => /* @translate */ 'receive E-Mail alert',
        		                   'description' => /* @translate */ 'if checked, you\'ll be informed by mail about new applications.'),
        		));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailAccessText',
        		'options' => array('label' => /* @translate */ 'Mailtext',
                                            'description' => /* @translate */ 'default text of the notification mail about new applications. The following variables can be used:<ul><li>##name## = your name</li><li>##title## = title of the job</li></ul>')));
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
                        'name' => 'mailConfirmationText',
                         'options' => array('label' => /* @translate */ 'Confirmation mail text',
                                            'description' => /* @translate */ 'default text of the acknowledgment of receipt mail to the applicant. The following variables can be used:<br><ul><li>##anrede_formell## = salutation. Includes gender, firstname and lastname.<li>##anrede_informell## = salutation. Includes fistname and lastname.</li><li>##job_title## = title of the jobs</li><li>##name## = name of the applicant.</li><li>##date## = date of recipt of the application.</li></ul>' )));
        
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailInvitationText',
        		'options' => array('label' => /* @translate */ 'Invitation mail text',
        		                    'description'=> /* @translate */ 'default text of the invitation mail to the applicant. You can use all variables of the acknowledgment of receipt mail. '
        		                    )));
        
        $this->add(array('type' => 'Zend\Form\Element\Textarea',
        		'name' => 'mailRejectionText',
        		'options' => array('label' => /* @translate */ 'Rejection mail text',
                                           'description' => /* @translate */ 'default text of the refusal of an application to the applicant. You can use all variables of the acknowledgment of receipt mail.')));
        
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'mailBCC',
        		'options' => array('label' => /* @translate */ 'get blind carbon copy of all own mails',
        		                   'description' => /* @translate */ 'if checked, you\'ll get a copy of all mails you send.',
                                           'value_options' => array(0,1, True, False))));
    }
        
}