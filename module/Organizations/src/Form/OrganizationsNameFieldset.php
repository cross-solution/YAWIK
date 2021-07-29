<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Organizations\Form;

use Core\Repository\RepositoryService;
use Laminas\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\Hydrator\Strategy\OrganizationNameStrategy;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Class OrganizationsFieldset
 * @package Organizations\Form
 */
class OrganizationsNameFieldset extends Fieldset implements InputFilterProviderInterface
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
            /* @var $formElementManager FormElementManager */
            $hydrator           = new EntityHydrator();

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
        return [
            'organizationName' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                ],
            ],
        ];
    }

    /**
     * @param object $object
     * @return $this|Fieldset|\Laminas\Form\FieldsetInterface
     */
    public function setObject($object)
    {
        parent::setObject($object);
        //$this->get('contact')->setObject($object->contact);
        //$this->populateValues($this->extract());
        return $this;
    }
}
