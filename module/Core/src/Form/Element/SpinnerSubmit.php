<?php

/** Rating.php */
namespace Core\Form\Element;

use Zend\Form\Element\Button;

/**
 * Star rating element.
 *
 */
class SpinnerSubmit extends Button implements ViewHelperProviderInterface
{
    protected $viewHelper = 'spinnerButton';

    protected $attributes = [
        'data-provide' => 'spinner-button',
    ];
    
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
