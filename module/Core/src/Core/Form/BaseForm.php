<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Core\Form;

class BaseForm extends Form
{
    
    protected $baseFieldset;
    
    public function init()
    {
        $this->addBaseFieldset();
        $this->addButtonsFieldset();
    }
    
    
    protected function addBaseFieldset()
    {
        if (null === $this->baseFieldset) {
            return;
        }
        
        $fs = $this->baseFieldset;
        if (!is_array($fs)) {
            $fs = array(
                'type' => $fs,
            );
        }
        if (!isset($fs['options']['use_as_base_fieldset'])) {
            $fs['options']['use_as_base_fieldset'] = true;
        }
        $this->add($fs);
    }
    
    protected function addButtonsFieldset()
    {
        $this->add(array(
            'type' => 'DefaultButtonsFieldset'
        ));
    }
    
}