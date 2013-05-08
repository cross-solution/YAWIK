<?php

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormCollection as ZendFormCollection;
use Zend\Form\Element\MultiCheckbox;

class FormCollection extends ZendFormCollection
{
    
    protected $useLabeledItems = true;
    
    
    public function useLabeledItems($flag=null)
    {
        if (null !== $flag) {
            $this->useLabeledItems = (bool) $flag;
            return $this;
        }
        return $this->useLabeledItems;
    }
    
    public function render(ElementInterface $element)
    {
        $this->setShouldWrap(false);
        
        if (!$element->hasAttribute('id')) {
            $element->setAttribute('id', str_replace(array('[', ']'), array('-', ''), $element->getName()));
        }
        
        $label = '';
        if ($this->useLabeledItems()) {
            $label = $element->getLabel();

            if (!empty($label)) {

                $label = $this->getView()->translate($label);
                $label = '<legend>' . $this->getEscapeHtmlHelper()->__invoke($label) . '</legend>';
            }
        }
        $markup = parent::render($element);
        
        if ($element instanceOf \Zend\Form\Element\Collection) {
            $useLabeledItems = $this->useLabeledItems();
            $this->useLabeledItems($element->getOption('use_labeled_items'));
            $markup = parent::render($element);   
            $this->useLabeledItems($useLabeledItems);
            
            $markup = sprintf(
                '<div id="%1$s-items" class="form-collection-items">%3$s</div>'
                . '<fieldset><button id="add-%1$s">%2$s</button></fieldset>',
                $element->getAttribute('id'),
                $this->getView()->translate('Add item'),
                $markup
            );
                
        }
        $markup = sprintf(
            '<fieldset id="%s">%s%s</fieldset>',
            $element->getAttribute('id'),
            $label,
            $markup
        );
        return $markup;                
    }
    
    /**
     * Only render a template
     *
     * @param  CollectionElement            $collection
     * @return string
     */
    public function renderTemplate(\Zend\Form\Element\Collection $collection)
    {
        if (!$collection->getOption('use_labeled_items')) {
            $collection->getTemplateElement()->setLabel('');
        }
        return parent::renderTemplate($collection);
        
    }
}
