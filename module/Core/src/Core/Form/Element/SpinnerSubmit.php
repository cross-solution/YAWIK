<?php

/** Rating.php */ 
namespace Core\Form\Element;

//use Zend\Form\Element;
//use Zend\Form\Element\Submit;

/**
 * Star rating element.
 * 
 */
class SpinnerSubmit extends AbstractElement
{
    protected $viewHelper = 'spinnerButton';
    protected $allowErrorMessages = false;
}