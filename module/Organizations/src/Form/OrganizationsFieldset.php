<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    Mathias Weitz <weitz@cross-solution.de>
 */

namespace Organizations\Form;

use Zend\Form\Fieldset;

/**
 * Class OrganizationsFieldset
 * @package Organizations\Form
 */
class OrganizationsFieldset extends Fieldset
{
    /**
     *
     */
    public function init()
    {
        $this->setName('organization');
        $this->add(array('type' => 'Organizations/ContactFieldset'));
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
