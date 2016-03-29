<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/** AttachmentsFieldset.php */
namespace Applications\Form;

use Zend\Form\Fieldset;

class CarbonCopyFieldset extends Fieldset
{
    /**
     * initialize carbon copy form
     */
    public function init()
    {
        $this->setName('carboncopy')
             ->setLabel('Options');

        $this->add(
            array(
                'type'    => 'Zend\Form\Element\Checkbox',
                'name'    => 'carboncopy',
                'options' => array(
                    'checked_value'   => '1',
                    'unchecked_value' => '0',
                    'label'           => 'send me a carbon copy',
                )
            )
        );
    }

    /**
     * Gets the input filter specification
     *
     * @return array
     */
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
