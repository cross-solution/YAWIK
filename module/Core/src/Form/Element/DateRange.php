<?php
/*
 * @todo use this in education/employments forms
 */

namespace Core\Form\Element;

use Zend\Form\Element;
use Zend\Form\Element\Date;
use Zend\Form\Element\Checkbox;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;

class DateRange extends Element implements ElementPrepareAwareInterface
{
    protected $startDateElement;
    protected $endDateElement;
    protected $currentCheckbox;
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        
        
        $this->startDateElement = new Date('startdate');
        $this->endDateElement   = new Date('enddate');
        $this->currentCheckbox  = new Checkbox(
            'current',
            array(
            'label' => 'Current',
            'use_hidden_element' => true,
            'value' => 0,
            )
        );
    }
    
    public function setOptions($options)
    {
        parent::setOptions($options);
        
        if (isset($options['startdate'])) {
            if (isset($options['startdate']['options'])) {
                $this->getStartDateElement()->setOptions($options['startdate']['options']);
            }
            if (isset($options['startdate']['attributes'])) {
                $this->getStartDateElement()->setAttributes($options['startdate']['attributes']);
            }
        }
        if (isset($options['enddate'])) {
            if (isset($options['enddate']['options'])) {
                $this->getEndDateElement()->setOptions($options['enddate']['options']);
            }
            if (isset($options['enddate']['attributes'])) {
                $this->getEndDateElement()->setAttributes($options['enddate']['attributes']);
            }
        }
    }
    protected function getDateElement($name, $options)
    {
        $elementSpec = isset($options[$name]) ? $options[$name] : '';
        $elementOptions = isset($elementSpec['options']) ? $elementSpec['options'] : array();
        $element = new Date($name, $elementOptions);
        if (isset($elementSpec['attributes'])) {
            $element->setAttributes($elementSpec['attributes']);
        }
        return $element;
    }
    
    public function getStartDateElement()
    {
        return $this->startDateElement;
    }
    
    public function getEndDateElement()
    {
        return $this->endDateElement;
    }
    
    public function getCurrentCheckbox()
    {
        return $this->currentCheckbox;
    }
    
    public function setValue($value)
    {
        $this->startDateElement->setValue($value['startDate']);
        $this->endDateElement->setValue($value['endDate']);
        if ($value['current']) {
            $this->endDateElement->setAttribute('class', 'hidden');
        }
        $this->currentCheckbox->setValue($value['current']);
    }
    
    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();
        
        $this->startDateElement->setName($name . '[startDate]');
        $this->endDateElement->setName($name . '[endDate]');
        $this->currentCheckbox->setName($name . '[current]');
    }
    
    public function __clone()
    {
        $this->startDateElement = clone $this->startDateElement;
        $this->endDateElement = clone $this->endDateElement;
        $this->currentCheckbox = clone $this->currentCheckbox;
    }
}
