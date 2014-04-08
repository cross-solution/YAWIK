<?php
/**

 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Core\Form\Element\ViewHelperProviderInterface as CoreElementInterface;

class FormElement extends ZendFormElement
{
    //protected $helper;
    
    //protected function getHelper() {
    //}
    
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof CoreElementInterface) {
            $helper = $element->getViewHelper();
            if (!is_string()) {
                $helper = $renderer->plugin($helper);
            }
            return $helper($element);
        }
        
        return parent::render($element);
    }
}