<?php

namespace Core\Form\View\Helper;


use Zend\View\Helper\AbstractHelper;
use Zend\Form\FieldsetInterface;

class FormPartial extends AbstractHelper
{
    
    public function __invoke($element)
    {        
        $renderer = $this->getView();
        $partial = $this->getPartial($element);
        return $renderer->partial($partial, array('element' => $element));
    }
        
    public function getPartial($element) 
    {
        if ($element instanceOf \Zend\Form\FieldsetInterface) {
            if ($element instanceOf \Core\Form\ViewPartialProviderInterface) {
                return $element->getViewPartial();
            }
            
            $type = preg_replace('~^.*\\\\~', '', get_class($element));
            $type = preg_replace_callback('~[A-Z]~', function($m) { return '-' . strtolower($m[0]); }, lcfirst($type));
            
            $partial = "form/$type";
            if (!$this->getView()->resolver($partial)) {
                $partial = "form/fieldset";
            }
            
            return $partial;
        } 
        
        return 'form/row';
         
    }
    
    
}
