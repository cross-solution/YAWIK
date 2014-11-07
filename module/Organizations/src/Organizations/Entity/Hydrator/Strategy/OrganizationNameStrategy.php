<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** HttploadStrategy.php */ 
namespace Organizations\Entity\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Organizations\Repository\OrganizationName as OrganizationNameRepository;

class OrganizationNameStrategy implements StrategyInterface
{
    protected $repository;
    
    public function __construct(OrganizationNameRepository $repository)
    {
        $this->repository = $repository;
        return $this;
    }
    
    public function extract ($value)
    {
        $name = '';
        if (isset($value->name)) {
            $name = $value->name;
        }
        return $name;
    }
    
    public function hydrate ($value)
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

