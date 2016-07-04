<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Filter;


use Solr\Bridge\Manager;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractPaginationQuery
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.27
 * @package Solr\Filter
 */
abstract class AbstractPaginationQuery implements FilterInterface
{
    /**
     * @var array
     */
    protected $sortPropertiesMap = array();

    /**
     * Store property name and converter to be used
     * during result conversion
     *
     * @var array
     */
    protected $propertiesMap = [];

    /**
     * @var Manager
     */
    protected $manager = null;

    /**
     * AbstractPaginationQuery constructor.
     * 
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Filter query based on given value
     *
     * @param mixed $value
     * @return \SolrQuery
     */
    public function filter($value)
    {
        $query = new \SolrQuery();
        
        return $this->createQuery($value,$query);
    }

    /**
     * Returns sort parameter to be used for query
     *
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

    /**
     * Returs an array key => value for this pagination filter
     * to define custom solr result handler
     * @return array
     * @codeCoverageIgnore
     */
    public function getPropertiesMap()
    {
        return $this->propertiesMap;
    }

    /**
     * Creates new instance for this filter
     *
     * @param ServiceLocatorInterface $sl
     * @return static
     */
    static public function factory(ServiceLocatorInterface $sl)
    {
        $manager = $sl->getServiceLocator()->get('Solr/Manager');
        return new static($manager);
    }

    /**
     * Returns class to be used for entity object creation
     *
     * @return string
     */
    abstract public function getEntityClass();

    /**
     * @param   array $params
     * @param   \SolrQuery $query
     * @return  \SolrQuery
     */
    abstract public function createQuery(array $params,$query);
}