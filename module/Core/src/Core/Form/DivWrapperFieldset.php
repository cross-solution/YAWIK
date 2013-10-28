<?php

namespace Core\Form;


use Zend\Form\Fieldset;

class DivWrapperFieldset extends Fieldset implements ViewPartialProviderInterface
{
    protected $viewPartial = 'form/div-wrapper-fieldset';

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