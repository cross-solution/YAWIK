<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    Carsten Bleek bleek@cross-solution.de
 */

namespace Admin\Form;

use Zend\Form\Fieldset;


/**
 * Class ConfigFieldset
 * @package Admin\Form
 */
class ConfigFieldset extends Fieldset
{

    public function init()
    {
        $this->setName('name');

        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => /* @translate */ 'Parameter'
            )
        ));

        $this->add(array(
            'name' => 'value',
            'options' => array(
                'label' => /* @translate */ 'Value'
            )
        ));

    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }

    /**
     * @param object $object
     * @return $this|Fieldset|\Zend\Form\FieldsetInterface
     */
    public function setObject($object)
    {
        parent::setObject($object);
        return $this;
    }
}
