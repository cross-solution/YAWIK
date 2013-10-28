<?php

namespace Core\Form;

use Zend\Form\Fieldset;

class ButtonsFieldset extends Fieldset implements ViewPartialProviderInterface
{
    protected $viewPartial = 'form/core/buttons';
    
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