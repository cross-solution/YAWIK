<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

class FormRowCombined extends AbstractHelper
{
    protected $layout;
     
    public function getFormRowHelper()
    {
        $helper = clone $this->view->plugin('formRow');
        $helper->setShouldWrap(false);
        return $helper;
    }
    
    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $elements
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(array $elements)
    {
        $formRowHelper = $this->getFormRowHelper();
        $labels = array();
        $markups = '';
        $totalSpanWidth = 0;
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
                '<div class="col-md-%d%s">%s</div>',
                $spanWidth,
                empty($elementErrors) ? '' : ' input-error',
                $markup
            );
            $totalSpanWidth += $spanWidth;
        }
        $labelSpanWidth = 12 - $totalSpanWidth;
        
        $labelMarkup = sprintf(
            '<div class="col-md-%d yk-label">%s</div>',
            $labelSpanWidth,
            implode(' / ', $labels)
        );
        
        $form_row_class = 'row';
        if ($this->layout == 'form-horizontal') {
            $form_row_class = 'form-group';
        }
        
        
        return sprintf(
            '<div class="controls controls-row ' . $form_row_class . '">%s%s</div>',
            $labelMarkup,
            $markups
        );
    }
    
    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param null|ElementInterface $element
     * @param null|string           $labelPosition
     * @param bool                  $renderErrors
     * @return string|FormRow
     */
    public function __invoke(array $elements = array(), $labelPosition = null, $renderErrors = null, $layout = null)
    {
        if (empty($elements)) {
            return $this;
        }
        
        if (null !== $layout) {
            $this->layout = $layout;
        }
        
        return $this->render($elements);
    }
}
