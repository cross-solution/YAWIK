<?php
/**
*
 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormCheckbox;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class ToggleButton extends FormCheckbox
{
    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @param String $buttonContent
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element, $buttonContent = null)
    {
        if (null === $buttonContent) {
            $buttonContent = $element->getLabel();
            if (null === $buttonContent) {
                throw new Exception\DomainException(
                    sprintf(
                        '%s expects either button content as the second argument, ' .
                        'or that the element provided has a label value; neither found',
                        __METHOD__
                    )
                );
            }

            if (null !== ($translator = $this->getTranslator())) {
                $buttonContent = $translator->translate(
                    $buttonContent,
                    $this->getTranslatorTextDomain()
                );
            }
            $element->setLabel('');
        }

        $value          = $element->getValue();
        $checkedBoole   = ($value == 1 || $value == 'on');
        

        $checkedClass   = $checkedBoole?'active"':'';

        $hiddenElement = '';
        if ($element->useHiddenElement()) {
            $hiddenAttributes = [
                'name'     => $element->getName(),
                'value'    => $element->getUncheckedValue(),
            ];

            $hiddenElement = sprintf(
                        '<input type="hidden" %s%s',
                        $this->createAttributesString($hiddenAttributes),
                        '>'
                    );
            $element->setUseHiddenElement(false);
        }

        $buttonContent = $hiddenElement . PHP_EOL
                . '<div class="btn-group" data-toggle="buttons">' . PHP_EOL
                . '<label class="btn btn-default ' . $checkedClass . '">' . PHP_EOL
                . parent::render($element) . $buttonContent . PHP_EOL . '</label>' . PHP_EOL
                . '</div>' . PHP_EOL;

        return $buttonContent;
    }
}
