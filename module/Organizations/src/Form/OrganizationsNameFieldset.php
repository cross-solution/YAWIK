<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Organizations\Form;

use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\Hydrator\Strategy\OrganizationNameStrategy;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;

/**
 * Class OrganizationsFieldset
 * @package Organizations\Form
 */
class OrganizationsNameFieldset extends Fieldset
{
    
    /**
     * @var RepositoryService
     */
    private $repositories;
    
    /**
     * @return RepositoryService
     */
    public function getRepositories()
    {
        return $this->repositories;
    }
    
    /**
     * @param RepositoryService $repositories
     */
    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            /* @var $formElementManager FormElementManagerV3Polyfill */
            $hydrator           = new EntityHydrator();
            $formFactory        = $this->getFormFactory();
            $formElementManager = $formFactory->getFormElementManager();
            
            $repositoryManager = $this->repositories;
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
                'label' => /* @translate */ 'Organization Name'
            ),
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
