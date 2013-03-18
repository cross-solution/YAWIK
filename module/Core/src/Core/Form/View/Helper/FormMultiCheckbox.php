<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormMultiCheckbox as BaseFormMultiCheckbox;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;

class FormMultiCheckbox extends BaseFormMultiCheckbox
{
    /**
     * Render options
     *
     * @param MultiCheckboxElement $element
     * @param array                $options
     * @param array                $selectedOptions
     * @param array                $attributes
     * @return string
     */
    protected function renderOptions(MultiCheckboxElement $element, array $options, array $selectedOptions,
        array $attributes)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper      = $this->getLabelHelper();
        $labelClose       = $labelHelper->closeTag();
        $labelPosition    = $this->getLabelPosition();
        $globalLabelAttributes = $element->getLabelAttributes();
        $closingBracket   = $this->getInlineClosingBracket();
    
        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }
    
        $combinedMarkup = array();
        if (isset($attributes['id'])) {
            $idPrefix = $attributes['id'] . '-';
            unset($attributes['id']);
        } else {
            $idPrefix = '';
        }
            
    
        foreach ($options as $key => $optionSpec) {
       
            
            $value           = '';
            $label           = '';
            $selected        = false;
            $disabled        = false;
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
    
            if (is_scalar($optionSpec)) {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key
                );
            }
    
            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }
            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }
            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = (isset($labelAttributes))
                ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                : $optionSpec['label_attributes'];
            }
            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }
    
            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }
    
            $inputAttributes['id'] = $idPrefix . $value;
            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;
    
            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );
    
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
    
            $labelAttributes['for'] = $inputAttributes['id'];
            $label     = $escapeHtmlHelper($label);
            $labelOpen = $labelHelper->openTag($labelAttributes);
            $labelTag  = $labelOpen . $label . $labelClose;
            //$template  = $labelOpen . '%s%s' . $labelClose;
            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = $labelTag . $input;
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = $input . $labelTag;
                    break;
            }
    
            $combinedMarkup[] = $markup;
        }
    
        return implode($this->getSeparator(), $combinedMarkup);
    }
    
}