<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AbstractPaginationQuery.php */ 
namespace Core\Repository\Filter;

use Zend\Filter\FilterInterface;

abstract class AbstractPaginationQuery implements FilterInterface
{
    
    protected $repositoryName;
    
    public function filter($value, $queryBuilder=null)
    {
        if (null === $queryBuilder) {
            throw new \DomainException('$queryBuilder must not be null');
        }
        
        if ($this->repositoryName) {
            $nameParts = explode('/', $this->repositoryName);
            $entityClass = $nameParts[0] . '\\Entity\\' . $nameParts[1];
            $queryBuilder->find($entityClass);
        }
        return $this->createQuery($value, $queryBuilder);
    }
    
    abstract public function createQuery($params, $queryBuilder);
}

