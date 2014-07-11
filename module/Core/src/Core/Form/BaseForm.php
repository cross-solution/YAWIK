<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Core\Form;

/**
 * Base YAWIK form.
 * 
 * This form adds a base fieldset and a button fieldset.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class BaseForm extends Form
{
    
    /**
     * Base fieldset
     * Can be a string representing a FormElementManager key (aka element type),
     * an array holding specification according to {@add()}.
     * 
     * @var string|array
     */
    protected $baseFieldset;
    
    /**
     * {@inheritDoc}
     * @see \Zend\Form\Element::init()
     * @uses addBaseFieldset()
     * @uses addButtonsFieldset()
     */
    public function init()
    {
        $this->addBaseFieldset();
        $this->addButtonsFieldset();
    }
    
    /**
     * Adds the base fieldset.
     * 
     * 
     */
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
    
    /**
     * Adds the buttons fieldset.
     */
    protected function addButtonsFieldset()
    {
        $this->add(array(
            'type' => 'DefaultButtonsFieldset'
        ));
    }
    
}