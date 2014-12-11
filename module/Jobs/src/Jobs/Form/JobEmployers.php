<?php

namespace Jobs\Form;

use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Defines the formular for entering the hiring organization name
 *
 * @package Jobs\Form
 */
class JobEmployers extends Form implements InputFilterProviderInterface
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
            'type' => 'Jobs/JobEmployersFieldset',
            'name' => 'employers',
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
        return array(
        );
    }
}