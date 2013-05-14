<?php

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;

class FormDateRange extends AbstractHelper
{
    
    protected $elementHelper;
    
    public function render(ElementInterface $element)
    {
        if (!$element instanceOf \Core\Form\Element\DateRange) {
            die (__METHOD__ . ': Element must be of Type DateRange');
        }
        
        $elementHelper = $this->getElementHelper();
        
        $startDateElement = $element->getStartDateElement();
        if ('' == $startDateElement->getLabel()) {
            $startDateElement->setLabel('Startdate');
        }
        if (!$startDateElement->hasAttribute('title')) {
            $startDateElement->setAttribute('title', 'Please enter the start date');
        }
        $startDate = $elementHelper->render($startDateElement);
        
        $endDateElement = $element->getEndDateElement();
        if ('' == $endDateElement->getLabel()) {
            $endDateElement->setLabel('Enddate');
        }
        if (!$endDateElement->hasAttribute('title')) {
            $endDateElement->setAttribute('title', 'Please enter the end date');
        }
        $endDate = $elementHelper->render($endDateElement);
        $endDate = preg_replace(
            '~</div>$~', 
            sprintf('<div id="%s-currenttext" class="daterange-currenttext%s">%s</div></div>',
                    $element->getAttribute('id'), 
                    $element->getCurrentCheckbox()->isChecked() ? '' : ' hidden',
                    ($text = $element->getOption('current_text')) ? $text : 'Until today'),
            $endDate
        );
        $current = $elementHelper->render($element->getCurrentCheckbox());
        
        return $startDate . $endDate . $current;
               
    }
    
    public function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('formrow');
        }

        return $this->elementHelper;
    }
    
    public function getCheckboxElementHelper()
    {
        if ($this->checkboxElementHelper) {
            return $this->checkboxElementHelper;
        }
    
        if (method_exists($this->view, 'plugin')) {
            $this->checkboxElementHelper = $this->view->plugin('formcheckbox');
        }
    
        return $this->checkboxElementHelper;
    }
    
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }
}
