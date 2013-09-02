<?php

namespace Cv\Form;

use Zend\Form\Fieldset;
use Cv\Entity\Education as EducationEntity;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\FormInterface;

class EducationFieldset extends Fieldset
{
    
    public function init()
    {
        $this->setName('education')
             ->setHydrator(new EntityHydrator())
             ->setObject(new EducationEntity())
             ->setLabel('Education');
        
        $this->add(array(
            'type' => 'DateSelect',
            'name' => 'startDate',
            'options' => array(
                'label' => /*@translate*/ 'Start date'
            )
        ));
        $this->add(array(
            'type' => 'DateSelect',
            'name' => 'endDate',
            'options' => array(
                'label' => /*@translate*/ 'End date'
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