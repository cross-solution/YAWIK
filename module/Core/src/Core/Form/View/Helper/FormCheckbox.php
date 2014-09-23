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
        $label      = $element->getOption('long_label');
        $headline   = $element->getOption('headline');

        if ($label) {
            $label = $translator->translate($label, $textDomain);
        }

        if ($headline) {
            $headline = '<h6>' . $translator->translate($headline, $textDomain) . '</h6>';
        }

        $markup = '
        <div class="form-checkbox-wrapper">
            <div class="pull-left">%s</div>
            <div class="form-checkbox-label"><label for="%s">%s</label></div>
        </div>';
        
        $markup = sprintf(
            $markup,
            $input, $element->getAttribute('id'), $label
        );
        
        return $headline . $markup;
    }

    /**
     * Renders a checkbox with the parent render method.
     *
     * @param ElementInterface $element
     *
     * @return string
     */
    public function renderBare(ElementInterface $element)
    {
        return parent::render($element);
    }
}