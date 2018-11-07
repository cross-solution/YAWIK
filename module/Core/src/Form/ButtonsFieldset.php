<?php

namespace Core\Form;

use Zend\Form\Fieldset;

class ButtonsFieldset extends Fieldset implements ViewPartialProviderInterface, DisableCapableInterface
{
    /**
     * Name of the view partial in the template map or file name in the script path.
     *
     * @var string
     */
    protected $viewPartial = 'form/core/buttons';

    protected $options = array(
        'is_disable_capable' => false,
    );
    
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->viewPartial;
    }

    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : false;
    }
}
