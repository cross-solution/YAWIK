<?php

namespace Jobs\Form;

use Core\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;

class JobTitleLocation extends Form implements InputFilterProviderInterface
{

    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('jobs-form');
        $this->setAttributes(array(
            'id' => 'jobs-form',
            'data-handle-by' => 'native'
        ));
 
        $this->add(array(
            'type' => 'Jobs/JobFieldset',
            'name' => 'jobTitleLocation',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
        ));
        
        $this->add(array(
            'type' => 'DefaultButtonsFieldset',
            'options' => array(
                'save_label' => 'new' == $this->getOption('mode')
                                ? /*@translate*/ 'Publish job'
                                : 'Save',
            ),
        ));
        

    }
    
    public function getInputFilterSpecification()
    {
        $formName = $this->getFormName();
        return array(
            $formName => array('type' => 'new' == $this->getOption('mode') ? 'Jobs/Location/New' : 'Jobs/Location/Edit')
        );
    }
}