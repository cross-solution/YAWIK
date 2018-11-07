<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Organizations\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\Organization;
use Zend\Form\Fieldset;

/**
 * Class OrganizationsProfileFieldset
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.30
 * @package Organizations\Form
 */
class OrganizationsProfileFieldset extends Fieldset
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

    public function init()
    {
        $this->setName('profile-setting');
        $this->add([
            'type' => 'select',
            'name' => 'profileSetting',
            'options' => [
                'label' => /*@translate*/ 'Setting',
                'value_options' => [
                    Organization::PROFILE_ALWAYS_ENABLE => /*@translate*/ 'Always enable profile',
                    Organization::PROFILE_ACTIVE_JOBS   => /*@translate*/ 'Enable only when active jobs available',
                    Organization::PROFILE_DISABLED      => /*@translate*/ 'Disabled viewing profile',
                ]
            ]
        ]);
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
        return $object instanceof Organization;
    }
}
