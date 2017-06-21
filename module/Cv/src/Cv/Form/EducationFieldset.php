<?php

namespace Cv\Form;

use Cv\Form\InputFilter\Education;
use Zend\Form\Fieldset;
use Cv\Entity\Education as EducationEntity;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;

class EducationFieldset extends Fieldset implements InputFilterProviderInterface, ViewPartialProviderInterface
{
    
    use ViewPartialProviderTrait;
    
    /**
     * View script for rendering
     *
     * @var string
     */
    protected $defaultPartial = 'cv/form/education';
    
    public function init()
    {
        $this->setName('education')
             ->setHydrator(new EntityHydrator())
             ->setObject(new EducationEntity());
        
        $this->add(
            array(
            'type' => 'Core/Datepicker',
            'name' => 'startDate',
            'options' => array(
                'label' => /*@translate*/ 'Start date',
                'data-width' => '50%',
                'class' => 'selectpicker'
            )
            )
        );
        $this->add(
            array(
            'type' => 'Core/Datepicker',
            'name' => 'endDate',
            'options' => array(
                'label' => /*@translate*/ 'End date'
            )
            )
        );
        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'currentIndicator',
                'options' => array(
                        'label' => /*@translate */ 'ongoing'
                )
            )
        );
        
        $this->add(
            array(
                'name' => 'competencyName',
                'options' => array(
                        'label' => /*@translate */ 'Degree'),
                'attributes' => array(
                        //'id' => 'education-competencyname',
                        'title' =>  /*@translate */ 'please enter the name of your qualification'
                ),
            )
        );
        
        $this->add(
            array(
                'name' => 'organizationName',
                'options' => array(
                        'label' => /*@translate */ 'Organization Name'),
                'attributes' => array(
                        //'id' => 'education-organizationname',
                        'title' =>  /*@translate */ 'please enter the name of the university or school'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'country',
                'options' => array(
                        'label' => /*@translate */ 'Country'),
                'attributes' => array(
                        //'id' => 'education-country',
                        'title' => /*@translate */ 'please select the country'
                ),
            )
        );
        
        $this->add(
            array(
                'name' => 'city',
                'options' => array(
                        'label' => /*@translate */ 'City'),
                'attributes' => array(
                        //'id' => 'education-city',
                        'title' => /*@translate */ 'please enter the name of the city'
                ),
            )
        );
        
        $this->add(
            array(
                'name' => 'description',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                        'label' => /*@translate */ 'Description',
                ),
                'attributes' => array(
                        //'id' => 'education-description',
                        'title' => /*@translate */ 'please enter a description',
                ),
            )
        );
               
    }

    /**
     *
     * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification()
    {
        return [
            'type' => Education::class
        ];
    }

    /**
     *
     * @see \Zend\Form\Form::setData()
     */
    public function populateValues($data)
    {
        if (isset($data['currentIndicator'])
            && isset($data['endDate'])
            && $data['currentIndicator']
        ) {
            // empty & hide endDate if currentIndicator is checked
            $data['endDate'] = '';
            $this->get('endDate')->setOption('rowClass', 'hidden');
        }
        
        return parent::populateValues($data);
    }
}
