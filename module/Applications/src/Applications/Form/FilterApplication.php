<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
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
        $this->setName('filterApplication')
                ->setLabel('OptionsFA')
                ->setAttributes(array('class' => 'form-inline'));

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
                'label' => /* @translate */ 'Search Job Title'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'status',
            'options' => array(
                'label' => /* @translate */ 'Status'
            ),
        ));

        
        $this->add(array('type' => 'ToggleButton',
            'name' => 'unread1',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => 'unread only',
            )
        ));
        
        
        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
            'name' => 'unread',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => 'unread only',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit', 
                'class' => 'btn btn-primary'
                ),
            'options' => array(
                'label' => /* @translate */ 'Suche'
            ),
        ));
        
        $this->add(array(
            'type' => 'href',
            'name' => 'clear',
            'options' => array(
                'label' => /* @translate */ 'ref1'
            ),
        ));
              
    }

}
