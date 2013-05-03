<?php

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormRow as BaseFormRow;
use Zend\Form\Element\MultiCheckbox;

class FormRow extends BaseFormRow
{
    
    public function render(ElementInterface $element)
    {
        if (!$element->hasAttribute('title') && !$element instanceOf MultiCheckbox && $element->getLabel()) {
            $element->setAttribute('title', $this->view->translate($element->getLabel()));
        }
        $idAttr = ($id = $element->getAttribute('id'))
                ? 'id="' . $id . '-wrapper" ' : '';
        
        $markup = '<div ' . $idAttr . 'class="form-element">'
                . parent::render($element)
                . '</div>';
        
        while (preg_match('~<fieldset><legend>(.*?)</legend>(.*?)</fieldset>~s', $markup, $match)) {
            $markup = str_replace(
                $match[0], 
                '<div class="hidden label">' . $match[1] . '</div>' . $match[2],
                $markup
            );
        }
        
        return $markup;
                
    }
}
