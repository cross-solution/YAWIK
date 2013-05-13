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
        
        $startDate = $elementHelper->render($element->getStartDateElement());
        $endDate = $elementHelper->render($element->getEndDateElement());
        $endDate = preg_replace(
            '~</div>$~', 
            sprintf('<div id="%s-currenttext" class="daterange-currenttext%s">%s</div></div>',
                    $element->getAttribute('id'), 
                    $element->getCurrentCheckbox()->isChecked() ? '' : ' hidden',
                    $element->getOption('current_text')),
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
