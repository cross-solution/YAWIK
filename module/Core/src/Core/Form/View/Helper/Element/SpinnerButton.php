<?php
/**
*
 */

namespace Core\Form\View\Helper\Element;

use Zend\Form\View\Helper\FormButton;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class SpinnerButton extends FormButton
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
        $openTag = $this->openTag($element);

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

        $escape = $this->getEscapeHtmlHelper();
        $translator = $this->getTranslator();
        $buttonContent = '<div><div class="processing yk-hidden"><span class="fa-spin yk-icon-spinner yk-icon"></span> ' . $translator->translate('processing', $this->getTranslatorTextDomain()) . '</div><div class="default">' . $escape($buttonContent) . '</div></div>';

        return $openTag . $buttonContent . $this->closeTag();
    }
}
