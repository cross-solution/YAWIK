<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

class FormRowCombined extends AbstractHelper
{
    
    public function getFormRowHelper()
    {
        $helper = clone $this->view->plugin('formrow');
        $helper->setShouldWrap(false);
        return $helper;
    }
    
    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(array $elements)
    {
        $formRowHelper = $this->getFormRowHelper();
        $labels = array();
        $markups = ''; $totalSpanWidth = 0;
        foreach ($elements as $spanWidth => $element) {
            $elementErrors = $element->getMessages();
            $markup = $formRowHelper->render($element);
            if (preg_match('~<label.*</label>~isU', $markup, $match)) {
                $labels[] = $match[0];
                $markup = str_replace($match[0], '', $markup);
            } else {
                //$labels[] = false;
            }
            $markups .= sprintf(
                '<div class="span%d%s">%s</div>',
                $spanWidth, empty($elementErrors) ? '' : ' input-error', $markup
            );
            $totalSpanWidth += $spanWidth;
        }
        $labelSpanWidth = 12 - $totalSpanWidth;
        
        $labelMarkup = sprintf(
            '<div class="span%d text-right">%s</div>',
            $labelSpanWidth, implode(' / ', $labels)
        );
        
        return sprintf(
            '<div class="controls controls-row row-fluid">%s%s</div>',
            $labelMarkup, $markups
        );
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
    public function __invoke(array $elements = array(), $labelPosition=null, $renderErrors = null)
    {
        if (empty($elements)) {
            return $this;
        }
        
        
        return $this->render($elements);
    }
    
}
