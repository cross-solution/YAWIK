<?php

namespace Cv\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;

/**
 * class for accessing CVs
 */
class Cv extends AbstractRepository
{
    /**
     * Gets a pointer to access a CV
     * 
     * @param array $params
     */
    public function getPaginatorCursor($params)
    {
        return $this->getPaginationQueryBuilder($params)
                    ->getQuery()
                    ->execute();
    }
    /**
     * Gets a query builder to search for CVs
     *
     * @param array $params
     * @return unknown
     */
    protected function getPaginationQueryBuilder($params)
    {
        $filter = $this->getService('filterManager')->get('Applications/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
    
        return $qb;
    }
}