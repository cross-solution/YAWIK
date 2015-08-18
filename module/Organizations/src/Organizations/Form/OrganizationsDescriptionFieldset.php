<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Class OrganizationsDescriptionFieldset
 * @package Organizations\Form
 */
class OrganizationsDescriptionFieldset extends Fieldset
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
        $this->setName('organizations');

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
        return array();
    }
}
