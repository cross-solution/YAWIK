<?php

namespace Core\Form\Element;

use Zend\Form\Element;

class AbstractElement extends Element implements ViewHelperProviderInterface 
{
    protected $viewHelper;
    protected $allowErrorMessages = true;
    
    public function getViewHelper() {
        return $this->viewHelper;
    }
    
    public function allowErrorMessages() {
        return $this->allowErrorMessages;
    }
}