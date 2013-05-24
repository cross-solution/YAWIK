<?php

namespace Applications\Form;

use Zend\Form\Fieldset;
use Applications\Model\Education as EducationModel;
use Core\Model\Hydrator\ModelHydrator;

class EducationFieldset extends Fieldset
{
    public function init()
    {
        $this->setName('education')
             ->setHydrator(new ModelHydrator())
             ->setObject(new EducationModel())
             ->setLabel('Education');
        
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
            'attributes' => array(
                //'id' => 'education-id'
            ),
        ))->add(array(
            'type' => 'Hidden',
            'name' => 'applicationId',
        ));
        
        $this->add(array(
            'name' => 'range',
            'type' => '\Core\Form\Element\DateRange',
            'options' => array(
                'startdate' => array(
                    'options' => array(
                        'label' => 'Startdate',
                    ),
                    'attributes' => array(
                        'title' => 'Please enter the start date.'
                    ),
                ),
                'enddate' => array(
                    'options' => array(
                        'label' => 'Enddate',
                    ),
                    'attributes' => array(
                        'title' => 'Please enter the end date.'
                    ),
                ),
                'current_text' => 'Until today',
            ),
        ));
        
        $this->add(array(
        		'name' => 'competencyName',
        		'options' => array(
        				'label' => /*@translate */ 'Degree'),
        		'attributes' => array(
        				//'id' => 'education-competencyname',
        				'title' =>  /*@translate */ 'please enter the name of your qualification'
        		),
        ));
        
        $this->add(array(
        		'name' => 'organizationName',
        		'options' => array(
        				'label' => /*@translate */ 'Organization Name'),
        		'attributes' => array(
        				//'id' => 'education-organizationname',
        				'title' =>  /*@translate */ 'please enter the name of the university or school'
        		),
        ));

        $this->add(array(
        		'name' => 'organizationCountry',
        		'options' => array(
        				'label' => /*@translate */ 'Country'),
        		'attributes' => array(
        				//'id' => 'education-country',
        				'title' => /*@translate */ 'please select the country'
        		),
        ));
        
        $this->add(array(
        		'name' => 'organizationCity',
        		'options' => array(
        				'label' => /*@translate */ 'City'),
        		'attributes' => array(
        				//'id' => 'education-city',
        				'title' => /*@translate */ 'please enter the name of the city'
        		),
        ));
        
        $this->add(array(
        		'name' => 'description',
        		'type' => 'Zend\Form\Element\Textarea',
        		'options' => array(
        				'label' => /*@translate */ 'Description',
        		),
        		'attributes' => array(
        				//'id' => 'education-description',
        				'title' => /*@translate */ 'please enter a description',	
        		),
        ));
               
    }
    
}