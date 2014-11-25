<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
/** AttachmentsFieldset.php */

namespace Applications\Form;

use Zend\Form\Form;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ArraySerializable as ArrayHydrator;

// implements ViewPartialProviderInterface, InputFilterProviderInterface

class FilterApplication extends Form
{

    protected $hydrator;

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new ArrayHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $options = $this->getOptions();
        $this->setName('search-applications-form')
                ->setLabel('OptionsFA')
                ->setAttributes(array(
                    'class' => 'form-inline',
                    'method' => 'get'));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'search',
            'options' => array(
                'label' => /* @translate */ 'Search'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'job_title',
            'options' => array(
                'label' => /* @translate */ 'Enter job title',
            ),
            'attributes' => array(
                'id' => 'job-filter',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'job',
            'attributes' => array(
                'id' => 'job-filter-value',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status',
            'options' => array(
                'label' => /* @translate */ 'Status'
            ),
        ));

        
        $this->add(array('type' => 'ToggleButton',
            'name' => 'unread',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => 'unread',
            )
        ));
        
        /*
        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
            'name' => 'unread',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => 'unread only',
            )
        ));
        */

        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'attributes' => array(
                'value' => "1",
                'type' => 'submit', 
                'class' => 'btn btn-primary'
                ),
            'options' => array(
                'label' => /* @translate */ 'Search'
            ),
        ));
        
        $this->add(array(
            'type' => 'href',
            'name' => 'clear',
            'options' => array(
                'label' => /* @translate */ 'Clear'
            ),
            'attributes' => array(
                'class' => 'btn btn-default',
                //'onClick' => 'window.location.href=\'' . $options['clearRef'] . '\''
                'onClick' => 'window.location.href=\'?clear=1\''
            ),
        ));
             
    }

}
