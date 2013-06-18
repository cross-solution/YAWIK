<?php


namespace Applications\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods;
use Applications\Model\Application as ApplicationModel;
use Applications\Form\Element\Phone;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Application extends Form
{
    public function __construct()
    {
        parent::__construct('application');
        
        $hydrator = new ClassMethods(false);
        $this->setHydrator($hydrator);
        $this->bind($model);
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'jobid',
        ));
        $this->addElements();
        
 #       $this->add(array(
 #       		'type' => 'Applications\Form\ContactFieldset',
 #       		'options' => array(
 #       				'use_as_base_fieldset' => true
 #       		)
 #       ));
        
    }
        
    public function addElements(){
        $contact = new Fieldset('contact');
        $contact->setLabel('Contact details');
        
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
        	'type' => 'Zend\Form\Element\Email',
            'options' => array(
                'label' => /* @translate */ 'Email'
            ),
            'attributes' => array(
                'id' => 'contact-email'
            )
        ));
        
        $this->add($contact);
        
        $education = new Fieldset('education');
        $education->setLabel('Education history');        
        $this->add($education);

        $skill = new Fieldset('skills');
        $skill->setLabel('Personal skills');
       
        $skill->add(array(
        		'name' => 'skills',
        		'type' => 'Zend\Form\Element\Collection',
        		'options' => array(
        				'label' => /*@translate*/ 'native language',
        				'allow_add' => true,
        				'count' => 1,
        				'should_create_template' => true,
        				'target_element' => array(
        	#					'type'=> 'Applications/Form/ContactFieldset'
        						),
        						
        		),
        		'attributes' => array(
        				'id' => 'skills-nativeLanguage'
        		)
        ));
                
        $this->add($skill);
        
        $work = new Fieldset('work');
        $work->setLabel('Employment history');
        
        $this->add($work);
        
        $attachment = new Fieldset('attachment');
        $attachment->setLabel('Attachment');
        
        $this->add($attachment);
        
            
        $this->add(array(
        		'type' => 'Zend\Form\Element\Csrf',
        		'name' => 'csrf'
        ));
        
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


