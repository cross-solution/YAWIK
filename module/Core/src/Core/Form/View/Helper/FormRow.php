<?php

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormRow as BaseFormRow;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Button;

class FormRow extends BaseFormRow
{
    
    public function render(ElementInterface $element)
    {
        
        
        
        
        $markup = parent::render($element);
        if (!in_array($element->getAttribute('type'), array('hidden', 'button', 'submit'))
            && false !== $element->getOption('use_div_wrapper')    
        ) {
            $markup = sprintf(
                '<div id="%s-wrapper" class="form-element form-element-%s">%s</div>',
                $element->getAttribute('id'),
                strtolower(substr(get_class($element), strrpos(get_class($element), '\\') + 1)),
                $markup
            );
        } else {
            $markup = preg_replace('~<label[^>]*>.*?</label>~', '', $markup);
        }
          
        
        while (preg_match('~<fieldset><legend>(.*?)</legend>(.*?)</fieldset>~s', $markup, $match)) {
            $markup = str_replace(
                $match[0], sprintf(
                    '<label for="%s">%s</label>%s', 
                    $element->getAttribute('id'),
                    $match[1], $match[2]
                ), $markup
            );
        }
        
        return $markup;
                
    }
}
