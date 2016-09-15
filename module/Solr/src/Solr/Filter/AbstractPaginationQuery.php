<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Filter;

use SolrDisMaxQuery;
use ArrayAccess;
use Zend\Filter\FilterInterface;

/**
 * Class AbstractPaginationQuery
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @package Solr\Filter
 */
abstract class AbstractPaginationQuery implements FilterInterface
{

    /**
     * Filter query based on given value
     *
     * @param mixed $value
     * @return SolrDisMaxQuery
     */
    public function filter($value)
    {
        return $this->createQuery($value, new SolrDisMaxQuery());
    }

    /**
     * @param mixed $entity
     * @param ArrayAccess $solrResult
     * @return mixed Instance of proxy
     */
    abstract public function proxyFactory($entity, ArrayAccess $solrResult);

    /**
     * Get repository to be used for result
     *
     * @return string
     */
    abstract public function getRepositoryName();

    /**
     * @param   array $params
     * @param   SolrDisMaxQuery $query
     * @return  SolrDisMaxQuery
     */
    abstract public function createQuery(array $params, SolrDisMaxQuery $query);
}