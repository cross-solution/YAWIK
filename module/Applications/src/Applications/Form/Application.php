<?php


namespace Applications\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods;
use Applications\Model\Application as ApplicationModel;

class Application extends Form
{
    
    public function __construct(ApplicationModel $model)
    {
        parent::__construct('application');
        
        $hydrator = new ClassMethods();
        $this->setHydrator($hydrator);
        $this->bind($model);
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'jobid',
        ));
        
        $contact = new Fieldset('contact');
        $contact->setLabel('Contact details');
        $contact->setHydrator($hydrator)
            ->setObject($model);
        
        $contact->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'title',
            'options' => array(
                'label' => 'Title',
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
            'name' => 'houseNumber',
            'options' => array(
                'label' => /* @translate */ 'House number'
            ),
            'attributes' => array(
                'id' => 'contact-number'
            )
        ));
        
        $contact->add(array(
            'name' => 'zipCode',
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
            'name' => 'phoneNumber',
            'options' => array(
                'label' => /*@translate*/ 'Phone number',
            ),
            'attributes' => array(
                'id' => 'contact-phonenumber'
            )
        ));
        
        $contact->add(array(
            'name' => 'mobileNumber',
            'options' => array(
                'label' => /*@translate*/ 'Mobile phone number',
            ),
            'attributes' => array(
                'id' => 'contact-mobilenumber'
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
    
    public function getInputFilterToo()
    {
        if ($this->filter) {
            return $this->filter;
        }
        
        $filter = new \Zend\InputFilter\InputFilter();
        
        $filter->add(array(
            'type' => 'Zend\InputFilter\InputFilter',
            array(
                'name' => 'firstname',
                'required' => true,
            ),
        ), 'contact');
        
        $this->setInputFilter($filter);
        return $filter;
    }
}