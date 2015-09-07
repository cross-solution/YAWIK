<?php

namespace Core\Form\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\FormInterface;

class FormPartial extends AbstractHelper
{
    
    
    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @param  null|boolean $partial
     * @return string
     */
    public function __invoke(FormInterface $form = null, $partial = null)
    {
        if (!$form) {
            return $this;
        }
        if (!$partial) {
            throw new \BadMethodCallException('Missing parameter $partial.');
        }
    
        return $this->getView()->render($partial, array('form' => $form));
    }
}
