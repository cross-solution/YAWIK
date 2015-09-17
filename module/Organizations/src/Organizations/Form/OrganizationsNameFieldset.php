<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Organizations\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\Hydrator\Strategy\OrganizationNameStrategy;

/**
 * Class OrganizationsFieldset
 * @package Organizations\Form
 */
class OrganizationsNameFieldset extends Fieldset
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator           = new EntityHydrator();
            $formFactory        = $this->getFormFactory();
            $formElementManager = $formFactory->getFormElementManager();
            $serviceLocator     = $formElementManager->getServiceLocator();

            $repositoryManager = $serviceLocator->get('repositories');
            $repOrganizationName = $repositoryManager->get('Organizations/OrganizationName');

            $organizationName = new OrganizationNameStrategy($repOrganizationName);
            $hydrator->addStrategy('organizationName', $organizationName);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }


    /**
     *
     */
    public function init()
    {
        $this->setName('name');

        $this->add(
            array(
            'name' => 'organizationName',
            'options' => array(
                'label' => /* @translate */ 'Organizationname'
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

    /**
     * @param object $object
     * @return $this|Fieldset|\Zend\Form\FieldsetInterface
     */
    public function setObject($object)
    {
        parent::setObject($object);
        //$this->get('contact')->setObject($object->contact);
        //$this->populateValues($this->extract());
        return $this;
    }
}
