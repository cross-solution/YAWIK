<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\Form as ZendForm;
use Zend\Form\FormInterface;
use Zend\Form\FieldsetInterface;

class Form extends ZendForm
{
    const LAYOUT_HORIZONTAL = 'form-horizontal';
    const LAYOUT_INLINE     = 'form-inline';
    const LAYOUT_VERTICAL   = '';
    
    
    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @return Form
     */
    public function __invoke(FormInterface $form = null, $layout=self::LAYOUT_HORIZONTAL)
    {
        if (!$form) {
            return $this;
        }
    
        return $this->render($form, $layout);
    }
    
    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form, $layout=self::LAYOUT_HORIZONTAL)
    {
        
        $class = $form->getAttribute('class');
        $class = preg_replace('~\bform-[^ ]+\b~', '', $class);
        $class .= ' ' . $layout;
        
        $form->setAttribute('class', $class);

        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
    
        $formContent = '';
    
        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element, true, $layout);
            } else {
                $formContent.= $this->getView()->formRow($element);
            }
        }
    
        return $this->openTag($form) . $formContent . $this->closeTag();
    }
    
}