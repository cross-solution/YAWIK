<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;
// implements ViewPartialProviderInterface, InputFilterProviderInterface

class CarbonCopyFieldset extends Fieldset 
{
    public function init() {
        $this->setName('carboncopy')
             ->setLabel('Options');
                     
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		           'name' => 'carboncopy',
             
                           'options' => array(
                                'checked_value' => '1',
                                'unchecked_value' => '0',
                                'label' => 'send me a carbon copy',
                                              )
                           ));
          
          
    }
    
        public function getInputFilterSpecification()
    {
        return array(
                'carboncopy' => array(
                        'required' => false,
                        'filters'  => array(
                                array('name' => '\Zend\Filter\StringTrim'),
                        ),
                ),
        );
    
    }
}