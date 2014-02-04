<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** RepositoryService.php */ 
namespace Core\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use Core\Entity\EntityInterface;

class RepositoryService
{
    protected $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }
    
    public function get($name)
    {
        $nameParts = explode('/', $name);
        if (2 > count($nameParts)) {
            $nameParts = array($name, $name);
            //throw new \InvalidArgumentException('Name must be in the format "Namespace/Entity")');
        }
        
        $namespace   = $nameParts[0];
        $entityName  = $nameParts[1];
        $entityClass = "\\$namespace\\Entity\\$entityName";
        
        $repository  = $this->dm->getRepository($entityClass); 
        return $repository;
    }
    
    public function createQueryBuilder()
    {
        return $this->dm->createQueryBuilder();
    }
    
    public function store(EntityInterface $entity)
    {
        $this->dm->persist($entity);
        $this->dm->flush();
        return $this;
    }
    
    public function remove(EntityInterface $entity)
    {
        $this->dm->remove($entity);
        $this->dm->flush();
        return $this;
    }
    
    public function detach(EntityInterface $entity)
    {
        $this->dm->detach($entity);
        return $this;
    }
    
    
}

