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

use Zend\Form\View\Helper\FormSelect as ZfFormSelect;
use Zend\Form\ElementInterface;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormSelect extends ZfFormSelect
{
    public function render(ElementInterface $element)
    {
        if ($element->hasAttribute('data-placeholder')) {
            $placeholder = $element->getAttribute('data-placeholder');
            $placeholder = $this->getTranslator()->translate(
                $placeholder,
                $this->getTranslatorTextDomain()
            );
            $element->setAttribute('data-placeholder', $placeholder);
        }
        return parent::render($element);
    }
}
