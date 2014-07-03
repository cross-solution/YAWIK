<?php

namespace Core\Form\View\Helper;

use Core\Form\ViewPartialProviderInterface;
use Core\Form\ExplicitParameterProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;
use Core\Form\Container;
use Zend\Form\View\Helper\AbstractHelper;
use Core\Form\SummaryForm;

class FormContainer extends AbstractHelper
{
    
    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @return Form
     */
    public function __invoke(Container $container = null, $layout=self::LAYOUT_INLINE, $parameter = array())
    {
        if (!$container) {
            return $this;
        }
    
        return $this->render($container, $layout, $parameter);
    }
    
    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(Container $container, $layout=self::LAYOUT_INLINE, $parameter = array())
    {
        
        $content = '';
    
        if ($container instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), array('element' => $container));
        }
        foreach ($container as $element) {
            $parameterPartial = $parameter;
            if ($element instanceOf ExplicitParameterProviderInterface) {
                $parameterPartial = array_merge($element->getParams(), $parameterPartial);
            }
            if ($element instanceOf ViewPartialProviderInterface) {
                $parameterPartial = array_merge(array('element' => $element, 'layout' => $layout), $parameterPartial);
                $content .= $this->getView()->partial(
                    $element->getViewPartial(), $parameterPartial 
                );
                
            } else if ($element instanceOf ViewHelperProviderInterface) {
                $helper = $element->getViewHelper();
                if (is_string($helper)) {
                    $helper = $this->getView()->plugin($helper);
                }
                $content .= $helper($element);
            } else if ($element instanceOf SummaryForm) {
                $content .= $this->getView()->summaryForm($element);
            } else if ($element instanceof Container) {
                $content.= $this->render($element, $layout, $parameter);
            } else {
                $content.= $this->getView()->form($element, $layout, $parameter);
            }
        }
        
        return $content;

    }
    
}