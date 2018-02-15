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
use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\OrganizationContact;

/**
 * Class OrganizationsContactFieldset
 *
 * @package Organizations\Form
 */
class OrganizationsContactFieldset extends Fieldset
{
    /**
     * Gets the Hydrator
     *
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator           = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }


    /**
     * Set the elements for the contact form
     */
    public function init()
    {
        $this->setName('contact');
        
        $this->add(
            array(
                'name' => 'street',
                'options' => array(
                        'label' => /* @translate */ 'Street'
                )
            )
        );
        
        $this->add(
            array(
                'name' => 'houseNumber',
                'options' => array(
                        'label' => /* @translate */ 'House Number'
                )
            )
        );
        
        $this->add(
            array(
                'name' => 'postalcode',
                'options' => array(
                        'label' => /* @translate */ 'Postal Code'
                )
            )
        );
        
        $this->add(
            array(
                'name' => 'city',
                'options' => array(
                        'label' => /* @translate */ 'City'
                )
            )
        );
        $this->add(
            [
                'name' => 'country',
                'options' => [
                    'label' => /* @translate */ 'Country'
                ]
            ]
        );
        $this->add(
            array(
                'name' => 'phone',
                'options' => array(
                    'label' => /* @translate */ 'Phone'
                )
            )
        );
        $this->add(
            array(
                'name' => 'fax',
                'options' => array(
                    'label' => /* @translate */ 'Fax'
                )
            )
        );

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
    public function allowObjectBinding($object)
    {
        return $object instanceof OrganizationContact;
    }
}
