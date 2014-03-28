<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormCollection as ZendFormCollection;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;

class FormCollection extends ZendFormCollection
{
   
    protected $layout;
    protected $isWithinCollection = false;
    
    public function __invoke(ElementInterface $element = null, $wrap = true, $layout = null)
    {
        if (!$element) {
            return $this;
        }
        
        if ($layout) {
            $this->setLayout($layout);
        }

        $this->setShouldWrap($wrap);

        return $this->render($element);
    }
    
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
    
    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }
    
        $markup           = '';
        $templateMarkup   = '';
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper    = $this->getElementHelper();
        $fieldsetHelper   = $this->getFieldsetHelper();
    
        $isCollectionElement = $element instanceOf CollectionElement;
        if ($isCollectionElement && $element->shouldCreateTemplate()) {
            $this->isWithinCollection = true;
            $templateMarkup = $this->renderTemplate($element);
            $this->isWithinCollection = false;
        }
    
        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceOf ViewPartialProviderInterface) {
                $markup .= $this->getView()->partial(
                    $elementOrFieldset->getViewPartial(), array('element' => $elementOrFieldset)
                );
            
            }else if ($elementOrFieldset instanceof FieldsetInterface) {
                if ($isCollectionElement) {
                    $this->isWithinCollection = true;
                }
                $markup .= $fieldsetHelper($elementOrFieldset);
                $this->isWithinCollection = false;
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset, null, null, $this->layout);
            }
        }
    
        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $markup .= $templateMarkup;
        }
    
        
        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $elementId = preg_replace(
                array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
                array('-'              , '-'    , ''       ),
                $element->getName()
            );
            if ($this->isWithinCollection) {
                $markup = sprintf('<fieldset id="%s"><a class="remove-item cam-form-remove"><i class="yk-icon yk-icon-minus"></i></a>%s</fieldset>', $elementId, $markup);
            } else {
                $label = $element->getLabel();
        
                if (empty($label) && $element->getOption('renderFieldset')) {
                    $markup = sprintf(
                        '<fieldset id="%s"><div class="fieldset-content">%s</div></fieldset>',
                        $elementId, $markup
                    );
                } else if (!empty($label)) {
        
                    
                    if (null !== ($translator = $this->getTranslator())) {
                        $label = $translator->translate(
                            $label, $this->getTranslatorTextDomain()
                        );
                    }
    
                    $label = $escapeHtmlHelper($label);
                    
                    if ($isCollectionElement) {
                        $extraLegend = '<a href="#" class="add-item cam-form-add"><i class="yk-icon yk-icon-plus"></i></a>';
                        $class  = ' class="form-collection"';
                        $divWrapperOpen = $divWrapperClose = '';
                    } else {
                        $extraLegend = $class = '';
                        $divWrapperOpen = '<div class="fieldset-content">';
                        $divWrapperClose = '</div>';
                    }
                    
                    $markup = sprintf(
                        '<fieldset id="%s"%s><legend>%s%s</legend>%s%s%s</fieldset>',
                        $elementId, $class, $label, $extraLegend,
                        $divWrapperOpen, $markup, $divWrapperClose
                    );
                    
                }
            }
        }
    
        return $markup;
    }
    
    
}