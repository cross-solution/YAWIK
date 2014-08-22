<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormCheckbox as ZfFormCheckbox;
use Zend\Form\ElementInterface;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormCheckbox extends ZfFormCheckbox
{
    
    public function render(ElementInterface $element)
    {
        $input      = parent::render($element);
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();
        $label      = $element->getLabel();
        $desc       = $element->getOption('description');
        
        if ($label) {
            $label = $translator->translate($label, $textDomain);
        }
        
        if ($desc) {
            $desc = $translator->translate($desc, $textDomain);
            $label = '<h6>' . $label . '</h6>';
        } else {
            $desc = $label;
            $label = '';
        }
        
        $markup = '
        <div class="form-checkbox-wrapper">
            <div class="pull-left">%s</div>
            <div class="form-checkbox-label"><label for="%s">%s</label></div>
        </div>';
        
        $markup = sprintf(
            $markup,
            $input, $element->getAttribute('id'), $desc
        );
        
        return $label . $markup;
    }
    
    public function renderBare(ElementInterface $element)
    {
        return parent::render($element);
    }
}