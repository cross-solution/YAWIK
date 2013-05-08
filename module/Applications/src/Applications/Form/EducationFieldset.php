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
        ));
        
        $this->add(array(
            'name' => 'startDate',
       		'type' => 'Zend\Form\Element\Date',
            'options' => array(
                'label' => /*@translate */ 'Startdate'
            ),
            'attributes' => array(
                //'id' => 'education-startdate',
            	'title' => /*@translate */ 'please enter the startdate'
            )
        ));
                
        $this->add(array(
            'name' => 'endDate',
        	'type' => 'Zend\Form\Element\Date',
        	'options' => array(
        		'label' => /*@translate */ 'Enddate'),
            'attributes' => array(
                //'id' => 'education-enddate',
            	'title' => /*@translate */ 'please enter the end date',
            ),
        ));

        $this->add(array(
        		'name' => 'currentIndicator',
        		'type' => 'Zend\Form\Element\Checkbox',
        		'options' => array(
        				'label' => /*@translate */ 'Current'
        		),
        		'attributes' => array(
        				//'id' => 'education-currentindicator',
        				'title' =>  /*@translate */ 'till now?'
        		)
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