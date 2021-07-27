<?php

declare(strict_types=1);

namespace Core\Paginator\Adapter;

use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Class DoctrineMongoAdapter
 * TODO: require count to be passed on constructor
 * @since 0.36
 * @package Core\Paginator\Adapter
 */
class DoctrineMongoAdapter implements AdapterInterface
{
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $builder;

    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $builder = $this->builder;
        $builder->skip($offset);
        $builder->limit($itemCountPerPage);
        $builder->getQuery()->getIterator()->toArray();

        return $builder->getQuery()->getIterator()->toArray();
    }

    /**
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function count()
    {
        $builder = clone $this->builder;
        $builder->count();
        return $builder->getQuery()->execute();
    }
}