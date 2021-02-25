<?php

declare(strict_types=1);

namespace Core\Paginator\Adapter;


use Core\Repository\Filter\AbstractPaginationQuery;
use Laminas\Paginator\Adapter\AdapterInterface;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

/**
 * Class DoctrineMongoLateAdapter
 *
 * TODO: require count to be passed on constructor
 * @since 0.36
 * @package Core\Paginator\Adapter
 */
class DoctrineMongoLateAdapter implements AdapterInterface
{
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    private $totalItem;

    /**
     * @param QueryBuilder $queryBuilder
     * @param AbstractPaginationQuery $filter
     * @param array $params
     */
    public function __construct(QueryBuilder $queryBuilder, AbstractPaginationQuery $filter, $params = array())
    {
        $this->queryBuilder = $queryBuilder;
        $filtered = $filter->createQuery($params, $queryBuilder);
        $this->queryBuilder = $queryBuilder;

        // skip count during tests
        if(!is_null($filtered)){
            $this->queryBuilder = $filtered;
            $this->totalItem = (clone $filtered)->count()->getQuery()->execute();
        }

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
     */
    public function count()
    {
        return $this->totalItem;
    }

}