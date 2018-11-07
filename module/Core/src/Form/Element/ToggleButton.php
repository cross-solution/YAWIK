<?php
namespace Core\Form\Element;

use Zend\Form\Element\Checkbox as ZendCheckbox;

/**
 *
 */
class ToggleButton extends ZendCheckbox implements ViewHelperProviderInterface
{
    /**
     * @var string
     */
    protected $viewHelper = 'toggleButton';
    
    /**
     *
     * @param string $helper
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
