<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Form;

use Laminas\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Class OrganizationsDescriptionFieldset
 * @package Organizations\Form
 */
class OrganizationsDescriptionFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }


    /**
     *
     */
    public function init()
    {
        $this->setName('organization-description');

        $this->add(
            array(
            'name' => 'description',
            'type' => 'textarea',
            'options' => array(
                'label' => /* @translate */ 'Description'
            )
            )
        );
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'description' => [
                'required' => true,
                'allow_empty' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                ],
            ],
        ];
    }
}
