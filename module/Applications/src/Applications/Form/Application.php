<?php


namespace Applications\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;

class Application extends Form
{
    
    public function __construct()
    {
        parent::__construct('application');
        
        $contact = new Fieldset('contact');
        $contact->setLabel('Contact details');
        
        $contact->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'title',
            'options' => array(
                //'label' => 'Title',
                'value_options' => array(
                    'mister' => /*@translate*/ 'Mister',
                    'miss' => /*@translate*/ 'Miss',
                ),
            ),
            'attributes' => array(
                'id' => 'contact-title'
            )
        ));
        
        $contact->add(array(
            'name' => 'firstname',
            'options' => array(
                'label' => /*@translate*/ 'First name'
            ),
            'attributes' => array(
                'id' => 'contact-firstname'
            )
        ));
        
        $contact->add(array(
            'name' => 'lastname',
            'options' => array(
                'label' => /*@translate */'Last name'
            ),
            'attributes' => array(
                'id' => 'contact-lastname'
            )
        ));
        
        $contact->add(array(
            'name' => 'street',
            'options' => array(
                'label' => /* @translate */ 'Street'
            ),
            'attributes' => array(
                'id' => 'contact-street'
            )
        ));
        
        $contact->add(array(
            'name' => 'number',
            'options' => array(
                'label' => /* @translate */ 'House number'
            ),
            'attributes' => array(
                'id' => 'contact-number'
            )
        ));
        
        $contact->add(array(
            'name' => 'zipcode',
            'options' => array(
                'label' => /* @translate */ 'Zip code'
            ),
            'attributes' => array(
                'id' => 'contact-zipcode'
            )
        ));
        
        $contact->add(array(
            'name' => 'location',
            'options' => array(
                'label' => /* @translate */ 'Location'
            ),
            'attributes' => array(
                'id' => 'contact-location'
            )
        ));
        
        $contact->add(array(
            'name' => 'email',
            'options' => array(
                'label' => /* @translate */ 'Email'
            ),
            'attributes' => array(
                'id' => 'contact-email'
            )
        ));
        
        $this->add($contact);
        
        $buttons = new Fieldset('buttons');
        
        $buttons->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => /* @translate */ 'Apply'
            )
        ));
        
        $this->add($buttons);
    }
}