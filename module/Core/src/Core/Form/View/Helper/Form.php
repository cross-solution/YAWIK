<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\Form as ZendForm;
use Zend\Form\FormInterface;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ExplicitParameterProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;

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
    public function __invoke(FormInterface $form = null, $layout=self::LAYOUT_INLINE, $parameter = array())
    {
        if (!$form) {
            return $this;
        }
    
        return $this->render($form, $layout, $parameter);
    }
    
    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form, $layout=self::LAYOUT_INLINE, $parameter = array())
    {
        
        $class = $form->getAttribute('class');
        $class = preg_replace('~\bform-[^ ]+\b~', '', $class);
        $class .= ' ' . $layout;
        
        $form->setAttribute('class', $class);

        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
    
        $formContent = '';
    
        if ($form instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), array('element' => $form));
        }
        foreach ($form as $element) {
            $parameterPartial = $parameter;
            if ($element instanceOf ExplicitParameterProviderInterface) {
                $parameterPartial = array_merge($element->getParams(), $parameterPartial);
            }
            if ($element instanceOf ViewPartialProviderInterface) {
                $parameterPartial = array_merge(array('element' => $element, 'layout' => $layout), $parameterPartial);
                $formContent .= $this->getView()->partial(
                    $element->getViewPartial(), $parameterPartial 
                );
                
            } else if ($element instanceOf ViewHelperProviderInterface) {
                $helper = $element->getViewHelper();
                if (is_string($helper)) {
                    $helper = $this->getView()->plugin($helper);
                }
                $formContent .= $helper($element);
            } else if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element, true, $layout);
            } else {
                $formContent.= $this->getView()->formRow($element, null, null, $layout);
            }
        }
    
        return $this->openTag($form) . $formContent . $this->closeTag();
    }
    
}