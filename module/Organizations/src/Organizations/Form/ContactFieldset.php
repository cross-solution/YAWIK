<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    Mathias Weitz <weitz@cross-solution.de>
 */

namespace Organizations\Form;

use Zend\Form\Fieldset;
use Organizations\Entity\OrganizationContact;

/**
 * Class ContactFieldset
 * @package Organizations\Form
 */
class ContactFieldset extends Fieldset 
{
    /**
     * set the elements for the form
     */
    public function init()
    {
        $this->setName('contact');
        
        $this->add(array(
        		'name' => 'street',
        		'options' => array(
        				'label' => /* @translate */ 'street'
        		)
        ));
        
        $this->add(array(
        		'name' => 'houseNumber',
        		'options' => array(
        				'label' => /* @translate */ 'house number'
        		)
        ));
        
        $this->add(array(
        		'name' => 'postalcode',
        		'options' => array(
        				'label' => /* @translate */ 'Postalcode'
        		)
        ));
        
        $this->add(array(
        		'name' => 'city',
        		'options' => array(
        				'label' => /* @translate */ 'City'
        		)
        ));
        
        //$this->add($fieldset);
        
    }

    /**
     * for later use - all the mandatory fields
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }

    /**
     * a required method to overwrite the generic method to make the binding of the entity work
     * @param object $object
     * @return bool
     */
    public function allowObjectBinding($object) {
        return $object instanceof OrganizationContact;
    }
}