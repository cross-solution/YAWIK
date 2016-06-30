<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Filter;


use Solr\Bridge\Manager;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

abstract class AbstractPaginationQuery implements FilterInterface
{
    /**
     * @var array
     */
    protected $sortPropertiesMap = array();

    public function filter($value)
    {
        $query = new \SolrQuery();
        
        return $this->createQuery($value,$query);
    }

    /**
     * @param   array $params
     * @param   \SolrQuery $query
     * @return  \SolrQuery
     */
    abstract public function createQuery(array $params,$query);

    /**
     * @param $sort
     * @return array
     */
    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = Manager::SORT_DESCENDING;
        } else {
            $sortProp = $sort;
            $sortDir = Manager::SORT_ASCENDING;
        }

        if (isset($this->sortPropertiesMap[$sortProp])) {
            $sortProp = $this->sortPropertiesMap[$sortProp];

            if (is_array($sortProp)) {
                return array_fill_keys($sortProp, $sortDir);
            }
        }

        return array($sortProp => $sortDir);
    }
}