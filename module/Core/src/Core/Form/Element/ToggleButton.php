<?php
namespace Core\Form\Element;

use Zend\Form\Element\Checkbox;

/**
 * 
 */
class ToggleButton extends Checkbox implements ViewHelperProviderInterface
{
    /**
     *
     * @var string 
     */
    protected $viewHelper = 'toggleButton';
    
    /**
     * 
     * @param type $helper
     * @return \Core\Form\Element\ToggleButton
     */
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