<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Paginator\Adapter;

use Zend\Paginator\Adapter\AdapterInterface;
use Doctrine\ODM\MongoDB\Cursor;

use Core\Repository\Filter\AbstractPaginationQuery;
use Doctrine\ODM\MongoDB\Query\Builder;
use Zend\Stdlib\Parameters;

/**
 * Class DoctrineMongoLateCursor
 * @package Core\Paginator\Adapter
 */
class DoctrineMongoLateCursor implements AdapterInterface
{

    protected $cursor;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var AbstractPaginationQuery
     */
    protected $filter;

    /**
     * @var Parameters
     */
    protected $params;

    /**
     * @param Builder $queryBuilder
     * @param AbstractPaginationQuery $filter
     */
    public function __construct(Builder $queryBuilder, AbstractPaginationQuery $filter)
    {
        $this->cursor = null;
        $this->builder = $queryBuilder;
        $this->filter = $filter;
        $this->params = new Parameters();
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        if (!$params instanceof Parameters) {
            $params = new Parameters($params);
        }
        $this->params = $params;
        return $this;
    }

    /**
     * @return AbstractPaginationQuery
     */
    protected function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return null
     */
    protected function getCursor()
    {
        if (!isset($this->cursor)) {
            $qb           = $this->getFilter()->filter($this->params, $this->builder);
            $this->cursor = $qb->getQuery()->execute();
        }
        //$adapter        = new \Core\Paginator\Adapter\DoctrineMongoCursor($cursor);
        return $this->cursor;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->getCursor()->count();
    }

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->getCursor()
            ->skip($offset)
            ->limit($itemCountPerPage)
            ->toArray();
    }
}
