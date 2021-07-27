<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** HttploadStrategy.php */
namespace Organizations\Entity\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Organizations\Repository\OrganizationName as OrganizationNameRepository;

class OrganizationNameStrategy implements StrategyInterface
{
    /**
     * @var $repository \Organizations\Repository\OrganizationName
     */
    protected $repository;

    public function __construct(OrganizationNameRepository $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @param mixed $value
     * @param object|null $object
     * @return mixed|string
     */
    public function extract($value, ?object $object = null)
    {
        $name = '';
        if (method_exists($value, "getName")) {
            $name = $value->getName();
        }
        return $name;
    }

    /**
     * @param mixed $value
     *
     * @param array|null $data
     * @return mixed|object
     */
    public function hydrate($value, ?array $data)
    {
        $organizationNameEntity = $value;
        if (is_string($value)) {
            if (!isset($this->repository)) {
                throw new \InvalidArgumentException('OrganizationNameStrategy needs to access to the Repository');
            }
            if (!$this->repository instanceof OrganizationNameRepository) {
                throw new \InvalidArgumentException('OrganizationNameStrategy repository needs to be of the class Organizations\Repository\OrganizationName');
            }
            $organizationNameEntity = $this->repository->findbyName($value);
        }
        return $organizationNameEntity;
    }
}
