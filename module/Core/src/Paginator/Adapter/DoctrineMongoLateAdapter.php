<?php

declare(strict_types=1);

namespace Core\Paginator\Adapter;


use Core\Repository\Filter\AbstractPaginationQuery;
use Laminas\Paginator\Adapter\AdapterInterface;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

class DoctrineMongoLateAdapter implements AdapterInterface
{
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    /**
     * @var AbstractPaginationQuery
     */
    private AbstractPaginationQuery $filter;

    private array $params;

    /**
     * DoctrineMongoLateAdapter constructor.
     * @param QueryBuilder $queryBuilder
     * @param AbstractPaginationQuery $filter
     * @param array $params
     */
    public function __construct(QueryBuilder $queryBuilder, AbstractPaginationQuery $filter, $params = array())
    {
        $this->queryBuilder = $queryBuilder;
        $this->filter = $filter;
        $this->params = $params;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        try {
            $qb = $this->queryBuilder;
            $qb->skip($offset)->limit($itemCountPerPage);
            return $qb->getQuery()->toArray();
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * FIXME: count method for ODM Module 3
     */
    public function count()
    {
        $qb = $this->queryBuilder;
        $total = $qb->getQuery()->execute()->toArray();
        return count($total);
    }

}