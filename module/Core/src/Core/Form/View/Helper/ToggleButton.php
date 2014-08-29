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
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element, $buttonContent = null)
    {
        $view = $this->getView();

        if (null === $buttonContent) {
            $buttonContent = $element->getLabel();
            if (null === $buttonContent) {
                throw new Exception\DomainException(sprintf(
                    '%s expects either button content as the second argument, ' .
                        'or that the element provided has a label value; neither found',
                    __METHOD__
                ));
            }

            if (null !== ($translator = $this->getTranslator())) {
                $buttonContent = $translator->translate(
                    $buttonContent, $this->getTranslatorTextDomain()
                );
            }
            $element->setLabel('');
        }

        $escape         = $this->getEscapeHtmlHelper();
        $translator     = $this->getTranslator();
        $name           = $element->getName();
        $value          = $element->getValue();
        $checkedBoole   = ($value == 1 || $value == 'on');
        
        $checked        = $checkedBoole?'checked="checked"':'';
        $checkedClass   = $checkedBoole?'active"':'';
        
        $buttonContent = '
        <div class="btn-group" data-toggle="buttons">' . PHP_EOL . '
            <span class="btn btn-default ' . $checkedClass . '">' . PHP_EOL . '
                ' . parent::render($element) . $buttonContent . PHP_EOL . '
            </span>' . PHP_EOL . '
        </div>' . PHP_EOL;

        return $buttonContent;
    }
}
