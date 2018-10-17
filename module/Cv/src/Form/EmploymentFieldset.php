<?php

namespace Cv\Form;

use Cv\Form\InputFilter\Employment;
use Zend\Form\Fieldset;
use Cv\Entity\Employment as EmploymentEntity;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;

class EmploymentFieldset extends Fieldset implements InputFilterProviderInterface, ViewPartialProviderInterface
{
    use ViewPartialProviderTrait;
    
    /**
     * View script for rendering
     *
     * @var string
     */
    protected $defaultPartial = 'cv/form/employment';
    
    public function init()
    {
        $this->setName('employment')
             ->setHydrator(new EntityHydrator())
             ->setObject(new EmploymentEntity());
        
        $this->add(
            array(
            'type' => 'Core/Datepicker',
            'name' => 'startDate',
            'options' => array(
                'label' => /*@translate */ 'Start date'
            )
            )
        );
        $this->add(
            array(
            'type' => 'Core/Datepicker',
            'name' => 'endDate',
            'options' => array(
                'label' => /*@translate */ 'End date'
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
                'name' => 'organizationName',
                'options' => array(
                        'label' => /*@translate */ 'Company Name'),
                'attributes' => array(
                        'title' =>  /*@translate */ 'please enter the name of the company'
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
                        'title' => /*@translate */ 'please describe your position',
                ),
            )
        );
    }
    
    /**
     * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification()
    {
        return [
            //'type' => 'Cv/Employment'
            'type' => Employment::class
        ];
    }
    
    /**
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
