<?php

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Core\Form\Element;

class FormElement extends ZendFormElement
{
    
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }
        if (!$element->hasAttribute('id')) {
            $element->setAttribute('id', strtolower(str_replace(array('[', ']'), array('-', ''), $element->getName())));
        }
        if ($element instanceof Element\DateRange) {
            $helper = $renderer->plugin('formdaterange');
            return $helper($element);
        }
        
        return parent::render($element);
    }
}
