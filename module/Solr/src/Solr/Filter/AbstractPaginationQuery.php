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
use Solr\Facets;
use DomainException;

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
     * @see \Zend\Filter\FilterInterface::filter()
     */
    public function filter($value, SolrDisMaxQuery $query = null, Facets $facets = null)
    {
        if (null === $query) {
            throw new DomainException('$query must not be null');
        }
        
        if (null === $facets) {
            throw new DomainException('$facets must not be null');
        }
        
        $this->createQuery($value, $query, $facets);
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
     * @param array $params
     * @param SolrDisMaxQuery $query
     * @param Facets $facets
     */
    abstract public function createQuery(array $params, SolrDisMaxQuery $query, Facets $facets);
}