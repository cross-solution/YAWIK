<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AbstractPaginationQuery.php */ 
namespace Core\Repository\Filter;

use Zend\Filter\FilterInterface;

abstract class AbstractPaginationQuery implements FilterInterface
{
    
    protected $repositoryName;
    protected $sortPropertiesMap = array();
    
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
    
    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }
        
        if (isset($this->sortPropertiesMap[$sortProp])) {
            $sortProp = $this->sortPropertiesMap[$sortProp];
        }
        
        return array($sortProp => $sortDir);
    }
}

