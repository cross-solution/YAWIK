<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license       MIT
 */

/** AttachmentsFieldset.php */
namespace Applications\Form;

use Laminas\Form\Fieldset;

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
                'type'    => 'Laminas\Form\Element\Checkbox',
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
                    array('name' => '\Laminas\Filter\StringTrim'),
                ),
            ),
        );
    }
}
