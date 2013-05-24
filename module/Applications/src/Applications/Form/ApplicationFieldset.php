<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\Application as ApplicationModel;
use Core\Model\Hydrator\ModelHydrator;

class ApplicationFieldset extends Fieldset
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new ModelHydrator();
            $arrayToModelCollectionStrategy = new \Core\Model\Hydrator\Strategy\ArrayToCollectionStrategy();
            $hydrator->addStrategy('educations', $arrayToModelCollectionStrategy);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        
        $this->setName('application')
             //->setHydrator(new ModelHydrator())
             ->setObject(new ApplicationModel());
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'jobid',
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'title',
            'options' => array(
                'label' => 'Choose your title',
                'value_options' => array(
                    'mister' => /*@translate*/ 'Mister',
                    'miss' => /*@translate*/ 'Miss',
                ),
            ),
            'attributes' => array(
                //'id' => 'contact-title',
                
            )
        ));
        
        $this->add(array(
            'name' => 'firstname',
            'options' => array(
                'label' => /*@translate*/ 'First name'
            ),
            'attributes' => array(
                //'id' => 'contact-firstname'
            )
        ));
        
        $this->add(array(
            'name' => 'lastname',
            'options' => array(
                'label' => /*@translate */'Last name'
            ),
            'attributes' => array(
                //'id' => 'contact-lastname'
            )
        ));
        
        $this->add(array(
            'name' => 'street',
            'options' => array(
                'label' => /* @translate */ 'Street'
            ),
            'attributes' => array(
                //'id' => 'contact-street'
            )
        ));
        
        $this->add(array(
            'name' => 'houseNumber',
            'options' => array(
                'label' => /* @translate */ 'House number'
            ),
            'attributes' => array(
                //'id' => 'contact-number'
            )
        ));
        
        $this->add(array(
            'name' => 'zipCode',
            'options' => array(
                'label' => /* @translate */ 'Zip code'
            ),
            'attributes' => array(
                //'id' => 'contact-zipcode'
            )
        ));
        
        $this->add(array(
            'name' => 'location',
            'options' => array(
                'label' => /* @translate */ 'Location'
            ),
            'attributes' => array(
                //'id' => 'contact-location'
            )
        ));
        
        $this->add(array(
            'name' => 'phoneNumber',
            'options' => array(
                'label' => /*@translate*/ 'Phone number',
            ),
            'attributes' => array(
                //'id' => 'contact-phonenumber'
            )
        ));
        
        $this->add(array(
            'name' => 'mobileNumber',
            'options' => array(
                'label' => /*@translate*/ 'Mobile phone number',
            ),
            'attributes' => array(
                //'id' => 'contact-mobilenumber'
            )
        ));
        
        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => /* @translate */ 'Email'
            ),
            'attributes' => array(
                //'id' => 'contact-email',
            	'title' => 'please enter a valid email address'
            )
        ));
        
        $this->add(array(
            'type' => 'Collection',
            'name' => 'educations',
            'options' => array(
                'label' => /*@translate */ 'education history',
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'collapsable' => true,
                'collapsed' => false,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'EducationFieldset'
                )
            ),
            'attributes' => array(
                //'id' => 'educations'
            ),
        ));
        
        $this->add(array(
        		'type' => 'Collection',
        		'name' => 'employments',
        		'options' => array(
        				'label' => /*@translate */ 'employment history',
        				'count' => 0,
        				'should_create_template' => true,
        		        'use_labeled_items' => false,
        		        'collapsable' => true,
        		        'collapsed' => false,
        				'allow_add' => true,
        				'target_element' => array(
        						'type' => 'EmploymentFieldset'
        				)
        		),
        ));
        
        
        $this->add(array(
        		'type' => 'Collection',
        		'name' => 'languages',
        		'options' => array(
        				'label' => /*@translate */ 'Languages',
        				'count' => 0,
        				'should_create_template' => true,
        		        'use_labeled_items' => false,
        		        'collapsable' => true,
        		        'collapsed' => false,
        				'allow_add' => true,
        		        'allow_remove' => true,
        				'target_element' => array(
        						'type' => 'LanguageFieldset'
        				)
        		),
        ));
        
        
    }
    
}