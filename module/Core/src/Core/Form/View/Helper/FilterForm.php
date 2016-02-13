<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\Form as ZendForm;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Form\Element\Text;

/**
 * Helper to render a summary form container.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
class FilterForm extends ZendForm
{
    public function render(FormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent .= $this->getView()->formCollection($element);
            } else {
                $label = $element->getLabel();
                if ($element instanceof Text) {
                    $element->setLabel('');
                    $element->setAttribute('placeholder', $label);
                }
                $formContent .= $this->getView()->formRow($element, null, null, Form::LAYOUT_BARE) . ' ';
            }
        }
        
        
        return $this->openTag($form) . $formContent . $this->closeTag();
    }
}
