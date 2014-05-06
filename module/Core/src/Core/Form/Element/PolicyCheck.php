<?php


namespace Core\Form\Element;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class PolicyCheck extends Fieldset implements ViewPartialProviderInterface, InputFilterProviderInterface
{    
    protected $viewPartial = 'form/core/privacy';
    protected $translator;
    
    public function injectTranslator($translator) {
        $this->translator = $translator;
        return $this;
    }
    
    protected function getTranslator() {
        return $this->translator;
    }
    
    public function init()
    {
  
    }
    
    public function getInputFilterSpecification()
    {
        return array();
    }
    
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->viewPartial;
    }
   
}