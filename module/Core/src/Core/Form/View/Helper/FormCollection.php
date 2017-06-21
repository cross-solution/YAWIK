<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormCollection as ZendFormCollection;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;

class FormCollection extends ZendFormCollection
{
   
    protected $layout;
    protected $isWithinCollection = false;
    protected $defaultElementHelper = 'formRow';
    
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
     * @param ElementInterface $element
     * @param bool $useViewPartial
     * @return string
     */
    public function render(ElementInterface $element, $useViewPartial = false)
    {
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        /* @var $elementHelper \Zend\Form\View\Helper\FormRow */
        $markup           = '';
        $templateMarkup   = '';
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper    = $this->getElementHelper();
        $fieldsetHelper   = $this->getFieldsetHelper();
    
        $isCollectionElement = $element instanceof CollectionElement;
        /* @var $element ElementInterface|CollectionElement */
        if ($isCollectionElement && $element->shouldCreateTemplate()) {
            $this->isWithinCollection = true;
            $templateMarkup = $this->renderTemplate($element);
            $this->isWithinCollection = false;
        }

        $elementId = $element->getAttribute('id');
        if (!$elementId) {
            $elementId = preg_replace(
                array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
                array('-'              , '-'    , ''       ),
                $element->getName()
            );

        }
        if ($formId = $element->getOption('__form_id__')) {
            $elementId = "$formId-$elementId";
        }

        $element->setAttribute('id', $elementId);

        /*
         * We had the problem, that collection templates were not rendered using the viewPartial due to the call
         * to this function from the parent class, which does not provide the $useViewPartial variable.
         * Currently this is fixed by always using the view partial if $this->isWithinCollection is true.
         *
         * We should consider using a new property $this->useViewPartial, for cover the case, where the
         * template should NOT be rendered using the view partial.
         *
         */
        if ($element instanceof ViewPartialProviderInterface && ($this->isWithinCollection || $useViewPartial)) {
            /* @var $partial \Zend\View\Helper\Partial */
            $partial = $renderer->plugin('partial');
            return $partial(
                $element->getViewPartial(), array('element' => $element)
            );
        }

        foreach ($element->getIterator() as $elementOrFieldset) {
            /* @var $elementOrFieldset ElementInterface|ViewPartialProviderInterface|ViewHelperProviderInterface */
            if ($elementOrFieldset instanceof ViewPartialProviderInterface) {
                $elementOrFieldsetId = $elementOrFieldset->getAttribute('id');
                if (!$elementOrFieldsetId) {
                    $elementOrFieldsetId = preg_replace(
                        array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
                        array('-'              , '-'    , ''       ),
                        $elementOrFieldset->getName()
                    );

                }
                if ($formId) { $elementOrFieldsetId = "$formId-$elementOrFieldsetId"; }
                $elementOrFieldset->setAttribute('id', $elementOrFieldsetId);
                /* @var $partial \Zend\View\Helper\Partial */
                $partial = $renderer->plugin('partial');
                $markup .= $partial(
                    $elementOrFieldset->getViewPartial(), array('element' => $elementOrFieldset)
                );
            
            } elseif ($elementOrFieldset instanceof FieldsetInterface) {
                if ($isCollectionElement) {
                    $this->isWithinCollection = true;
                }
                if ($elementOrFieldset instanceof ViewHelperProviderInterface) {
                    $helper = $elementOrFieldset->getViewHelper();
                    if (is_string($helper)) {
                        $helper = $renderer->plugin($helper);
                    }
                    $markup .= $helper($element);
                } else {
                    $markup .= $fieldsetHelper($elementOrFieldset);
                }
                $this->isWithinCollection = false;
            } elseif (false !== $elementOrFieldset->getOption('use_formrow_helper')) {
                $markup .= $elementHelper($elementOrFieldset, null, null, $this->layout);
            } else {
                /* @var $formElement \Zend\Form\View\Helper\FormElement */
                $formElement = $renderer->plugin('formelement');
                $formElement->render($elementOrFieldset);
            }

        }
    
        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $markup .= $templateMarkup;
        }
    
        
        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            if ($this->isWithinCollection) {
                $attrStr = $this->createAttributesString($element->getAttributes());
                $markup = sprintf('<fieldset %s><a class="remove-item yk-form-remove"><i class="yk-icon yk-icon-minus"></i></a>%s</fieldset>', $attrStr, $markup);
            } else {
                $label = $element->getLabel();

                if (empty($label) && $element->getOption('renderFieldset')) {
                    $attrStr = $this->createAttributesString($element->getAttributes());
                    $markup = sprintf(
                        '<fieldset %s><div class="fieldset-content">%s</div></fieldset>',
                        $attrStr,
                        $markup
                    );
                } elseif (!empty($label)) {
                    if (null !== ($translator = $this->getTranslator())) {
                        $label = $translator->translate(
                            $label,
                            $this->getTranslatorTextDomain()
                        );
                    }
    
                    $label = $escapeHtmlHelper($label);
                    
                    if ($isCollectionElement) {
                        $extraLegend = '<a href="#" class="add-item yk-form-add"><i class="yk-icon yk-icon-plus"></i></a>';
                        $class  = $element->getAttribute('class');
                        $class .= " form-collection";
                        $element->setAttribute('class', $class);
                        $divWrapperOpen = $divWrapperClose = '';
                    } else {
                        $extraLegend = $class = '';
                        $divWrapperOpen = '<div class="fieldset-content">';
                        $divWrapperClose = '</div>';
                    }
                    $attrStr = $this->createAttributesString($element->getAttributes());
                    
                    $markup = sprintf(
                        '<fieldset %s><legend>%s%s</legend>%s%s%s</fieldset>',
                        $attrStr,
                        $label,
                        $extraLegend,
                        $divWrapperOpen,
                        $markup,
                        $divWrapperClose
                    );
                    
                }
            }
        }
    
        return $markup;
    }
}
