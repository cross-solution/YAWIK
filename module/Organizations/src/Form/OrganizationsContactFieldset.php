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

use Laminas\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;
use Organizations\Entity\OrganizationContact;

/**
 * Class OrganizationsContactFieldset
 *
 * @package Organizations\Form
 */
class OrganizationsContactFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * Gets the Hydrator
     *
     * @return \Laminas\Hydrator\HydratorInterface
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

    public function getInputFilterSpecification()
    {
        return [
            'street' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
            'houseNumber'  => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
            'postalcode' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
            'city' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
            'country' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
            'phone' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
            'fax' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags']
                ],
            ],
        ];
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
