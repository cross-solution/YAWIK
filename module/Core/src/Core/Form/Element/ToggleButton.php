<?php
namespace Core\Form\Element;

use Zend\Form\Element\Checkbox;

/**
 * 
 */
class ToggleButton extends Checkbox implements ViewHelperProviderInterface
{
    protected $viewHelper = 'toggleButton';
    
    public function setViewHelper($helper)
    {
        $this->viewHelper = $helper;
        return $this;
    }
    
    public function getViewHelper()
    {
        return $this->viewHelper;
    }
}