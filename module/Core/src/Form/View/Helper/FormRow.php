<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\ElementInterface;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Element\Button;

class FormRow extends ZendFormRow
{
    protected $layout;
    protected $shouldWrap = true;
    protected $labelSpanWidth = 3;
    
    
    public function setShouldWrap($flag)
    {
        $this->shouldWrap = (bool) $flag;
        return $this;
    }
    
    public function shouldWrap()
    {
        return $this->shouldWrap;
    }
    
    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(ElementInterface $element, $ignoreViewPartial = false)
    {
        $labelAttributes = $element->getLabelAttributes();
        $labelWidth = $element->getOption('labelWidth');
        if (!$labelWidth) {
            $labelWidth = $this->labelSpanWidth;
        }

        if ($element instanceof ViewPartialProviderInterface && !$ignoreViewPartial) {
            return $this->getView()->partial(
                $element->getViewPartial(),
                                             array(
                                                 'element' => $element,
                                                 'labelWitdh' => $labelWidth,
                                                 'label_attributes' => $labelAttributes
                                                            )
            );
        }

        $escapeHtmlHelper    = $this->getEscapeHtmlHelper();
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
    
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors   = $elementErrorsHelper->render($element);
        
        // general Class
        $form_row_class = 'row';
        if ($this->layout == Form::LAYOUT_HORIZONTAL) {
            $form_row_class = 'form-group';
        }
        
        if (($elementRowClass = $element->getOption('rowClass')) != '') {
            $form_row_class .= ' '.$elementRowClass;
        }
        
        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass) && $this->layout != Form::LAYOUT_BARE) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;
    
            $element->setAttribute('class', $classAttributes);
        }
    
        if (!$element->hasAttribute('id')) {
            $elementId = preg_replace(
                array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
                array('-'             , '-',     ''),
                $element->getName()
            );
            $element->setAttribute('id', $elementId);
        } else {
            $elementId = $element->getAttribute('id');
        }
        /*
         * add form-control class to all form elements, but "submit" or "reset" and Buttons!
         */
        if ($element->getAttribute('type') != 'submit'
            && $element->getAttribute('type') != 'reset'
            && $element->getAttribute('type') != 'checkbox'
            && !$element instanceof Button
        ) {
            $element->setAttribute('class', $element->getAttribute('class').' form-control ');
        }
        
        $elementString = $elementHelper->render($element, $ignoreViewPartial);
        $desc = $element->getOption('description', false);
        if ($desc && $this->layout != Form::LAYOUT_BARE) {
            if (null !== ($translator = $this->getTranslator())) {
                $desc = $translator->translate(
                                 $desc,
                                 $this->getTranslatorTextDomain()
                             );
            }
            $elementString .= sprintf(
                '<div id="%s-desc" class="cam-description alert alert-info">%s</div>',
                $elementId,
                $desc
            );
        }
        
        if (!$element instanceof \Zend\Form\Element\Hidden
            && !$element instanceof \Zend\Form\Element\Button
            && $this->layout != Form::LAYOUT_BARE
        ) {
            $elementString .= sprintf(
                '<div id="%s-errors" class="errors">%s</div>',
                $elementId,
                $elementErrors
            );
        }
        
        // moved label here so we can change it in the ElementViewHelper
        $label           = $element->getLabel();
        if (isset($label) && '' !== $label && !$element instanceof \Zend\Form\Element\Button) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
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
                    $elementString
                );
            } else {
                if ($this->layout == Form::LAYOUT_BARE) {
                    $markup = $elementString;
                } else {
                    if (!isset($labelAttributes['for'])) {
                        $labelAttributes['for'] = $elementId;
                    }
                    if (Form::LAYOUT_HORIZONTAL == $this->layout) {
                        if (!isset($labelAttributes['class'])) {
                            $labelAttributes['class'] = '';
                        }
                        $labelAttributes['class'] .= ' control-label';
                    }
                    $element->setLabelAttributes($labelAttributes);

                    $label = $labelHelper($element, $label);
                    if ($this->shouldWrap) {
                        $spanWidth = 12 - $labelWidth;
                        $elementString = sprintf(
                            '<div class="col-md-%d%s" id="' . $elementId . '-span">%s</div>',
                            $spanWidth,
                            $elementErrors ? " $inputErrorClass" : '',
                            $elementString
                        );
                        $label = sprintf(
                            '<div class="col-md-%d yk-label">%s</div>',
                            $labelWidth,
                            $label
                        );
                    }
                    $markup = $label . $elementString;
                }
            }
        } else {
            if ($this->shouldWrap
                && !$element instanceof \Zend\Form\Element\Hidden
                && !$element instanceof \Zend\Form\Element\Button
                && $this->layout != Form::LAYOUT_BARE
                ) {
                $elementString = sprintf(
                    '<div class="col-md-12">%s</div>',
                    $elementString
                );
            }
            $markup = $elementString;
        }
        if ($this->shouldWrap
            && !$element instanceof \Zend\Form\Element\Hidden
            && !$element instanceof \Zend\Form\Element\Button
            && $this->layout != Form::LAYOUT_BARE
            ) {
            $markup = sprintf('<div class="controls controls-row ' . $form_row_class . '">%s</div>', $markup);
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
    public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $layout = null)
    {
        if (!$element) {
            return $this;
        }
    
        if ($labelPosition !== null) {
            $this->setLabelPosition($labelPosition);
        } else {
            $this->setLabelPosition(self::LABEL_PREPEND);
        }
    
        if ($renderErrors !== null) {
            $this->setRenderErrors($renderErrors);
        }
        
        if (null !== $layout) {
            $this->layout = $layout;
        }
    
        return $this->render($element);
    }
}
