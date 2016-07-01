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
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as EntityType;

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

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function filter($value)
    {
        $query = new \SolrQuery();
        
        return $this->createQuery($value,$query);
    }

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