<?php
/**

 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Core\Form\Element\ViewHelperProviderInterface as CoreElementInterface;
use Zend\View\Helper\HelperInterface;

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
            if (is_string($helper)) {
                $helper = $renderer->plugin($helper);
            }
            if ($helper instanceof HelperInterface) {
                $helper->setView($renderer);
            }
            return $helper($element);
        }
        
//         $type = $element->getAttribute('type');
        
//         if ('checkbox' == $type) {
//             $helper = $renderer->plugin('formcheckbox');
//             return $helper($element);
//         }
        
        return parent::render($element);
    }
}