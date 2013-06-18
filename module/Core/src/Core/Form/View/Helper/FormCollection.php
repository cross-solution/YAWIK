<?php

namespace Core\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormCollection as ZendFormCollection;
use Zend\Form\Element\MultiCheckbox;

class FormCollection extends ZendFormCollection
{
    
    protected $useLabeledItems = true;
    protected $insideCollection = false;
    
    public function isInsideCollection($flag = null)
    {
        if (null !== $flag) {
            $this->insideCollection = (bool) $flag;
            return $this;
        }
        return $this->insideCollection;
    }
    
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
                $label = '<legend>' . $this->getEscapeHtmlHelper()->__invoke($label);
                if ($element->getOption('collapsable')) {
                    $iconDir = $element->getOption('collapsed') ? 'n' : 's';
                    $label .= '<span class="float-right ui-icon ui-icon-triangle-1-' . $iconDir . '"></span>';
                } 
                
                $label .= '</legend>';
            }
        }
        $markup = parent::render($element);
        $class = '';
        if ($element instanceOf \Zend\Form\Element\Collection) {
            
            $useLabeledItems = $this->useLabeledItems();
            $isInsideCollection = $this->isInsideCollection();
            $this->useLabeledItems($element->getOption('use_labeled_items'));
            $this->isInsideCollection(true);
            $markup = parent::render($element);   
            $this->useLabeledItems($useLabeledItems);
            $this->isInsideCollection($isInsideCollection);
            $class = ' class="form-collection"';
            
            $markup = sprintf(
                '<div id="%1$s-items" class="form-collection-items">%3$s</div>'
                . '<fieldset class="form-collection-add-item"><button id="add-%1$s">%2$s</button></fieldset>',
                $element->getAttribute('id'),
                $this->getView()->translate('Add item'),
                $markup
            );
                
        }
        $removeIcon = $this->isInsideCollection()
                    ? '<button id="remove-' . $element->getAttribute('id') . '"'
                      . ' class="remove-collection-item-button"></button>'
                      
                    : '';
        
        $markup = sprintf(
            '<div id="%1$s-wrapper" class="fieldset-wrapper">%2$s<fieldset id="%1$s"%3$s>%4$s<div class="fieldset-content%5$s">%6$s</div></fieldset></div>',
            $element->getAttribute('id'),
            $removeIcon,
            $class,
            $label,
            $element->getOption('collapsed') ? ' hidden' : '',
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
