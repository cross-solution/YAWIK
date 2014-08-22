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
        return $value;
    }
    
    public function hydrate ($value)
    {
        $organizationNameEntity = $value;
        if (is_string($value)) {
            $organizationNameEntity = $this->repository->findOneBy(array('name' => $value));
            if (empty($organizationNameEntity)) {
                $organizationNameEntity = $this->repository->create();
                $organizationNameEntity->setName($value);
            }
        }
        return $organizationNameEntity;
    }
}

