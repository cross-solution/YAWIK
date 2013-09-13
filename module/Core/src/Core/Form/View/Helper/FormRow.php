<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\ElementInterface;

class FormRow extends ZendFormRow
{
    
    protected $layout;
    
    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(ElementInterface $element)
    {
        $escapeHtmlHelper    = $this->getEscapeHtmlHelper();
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
    
        $label           = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors   = $elementErrorsHelper->render($element);
    
        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;
    
            $element->setAttribute('class', $classAttributes);
        }
    
        if (!$element->hasAttribute('id')) {
            $elementId = preg_replace(
                array('~[^A-Za-z0-9]~', '~--+~', '~^-|-$~'),
                array('-'             , '-',     ''),
                $element->getName()
            );
            $element->setAttribute('id', $elementId);
        } else {
            $elementId = $element->getAttribute('id');
        }
        $elementString = $elementHelper->render($element);
        if ('form-horizontal' == $this->layout && !$element instanceOf \Zend\Form\Element\Hidden) {
            $elementString = sprintf(
                '<div class="controls">%s</div>',
                $elementString
            );
        }
    
        if (isset($label) && '' !== $label && !$element instanceOf \Zend\Form\Element\Button) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
    
            $label = $escapeHtmlHelper($label);
            $labelAttributes = $element->getLabelAttributes();
    
            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }
    
            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            $type = $element->getAttribute('type');
            if ($type === 'multi_checkbox' || $type === 'radio') {
                $markup = sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $elementString);
            } else {
                
                $labelAttributes = $element->getLabelAttributes();
                if (!isset($labelAttributes['for'])) {
                    $labelAttributes['for'] = $elementId;
                }
                if ('form-horizontal' == $this->layout) {
                    if (!isset($labelAttributes['class'])) {
                        $labelAttributes['class'] = '';
                    }
                    $labelAttributes['class'] .= ' control-label';
                }
                $element->setLabelAttributes($labelAttributes);
                
                
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper($element);
                
                switch ($this->labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup = $labelOpen . $label . $elementString . $labelClose;
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup = $labelOpen . $elementString . $label . $labelClose;
                        break;
                }
            }
    
            if ($this->renderErrors) {
                $markup .= $elementErrors;
            }
        } else {
            if ($this->renderErrors) {
                $markup = $elementString . $elementErrors;
            } else {
                $markup = $elementString;
            }
        }
        if ('form-horizontal' == $this->layout && !$element instanceOf \Zend\Form\Element\Hidden) {
            $markup = sprintf('<div class="control-group">%s</div>', $markup);
        }
    
        return $markup;
    }
    
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param null|ElementInterface $element
     * @param null|string           $labelPosition
     * @param bool                  $renderErrors
     * @return string|FormRow
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $layout=null)
    {
        if (!$element) {
            return $this;
        }
    
        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
        } else {
            $this->setLabelPosition(self::LABEL_PREPEND);
        }
    
        if ($renderErrors !== null){
            $this->setRenderErrors($renderErrors);
        }
        
        if (null !== $layout) {
            $this->layout = $layout;
        }
    
        return $this->render($element);
    }
    
}
