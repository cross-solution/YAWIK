<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as EntityType;
use Jobs\Repository\Job as JobRepository;
use Solr\Filter\AbstractPaginationQuery;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ResultConverter
 *
 * Convert SOLR query result into Doctrine ODM Entity
 * 
 * @author  Anthonius Munthi <me@itstoni.com>
 * @package Solr\Bridge
 * @since   0.26
 */
class ResultConverter
{
    /**
     * Current filter used for conversion
     *
     * @var AbstractPaginationQuery
     */
    protected $filter;

    /**
     * if set, the city name of the found location overwrites the general job location
     *
     * @var bool
     */
    protected $useGeoLocation=false;

    /**
     * @var ServiceLocatorInterface
     */
    protected $sl;

    public function __construct(ServiceLocatorInterface $sl)
    {
        $this->sl = $sl;
    }

    /**
     * Convert result into entity
     *
     * @param   AbstractPaginationQuery $filter
     * @param   \SolrQueryResponse $queryResponse
     * @return  EntityType[]
     */
    public function convert($filter, $queryResponse)
    {
        $this->filter = $filter;
        $response = $queryResponse->getResponse();
        $facets = $response['facet_counts'];
        $class = $filter->getProxyClass();
        $entities = [];
        $solrObjects = [];

        foreach($response['response']['docs'] as $doc){
            $solrObjects[$doc->id] = $doc;
        }

        /* @var JobRepository $repository */
        $keys = array_keys($solrObjects);
        $repository = $this->sl->get('repositories')->get($filter->getRepositoryName());
        $qb = $repository->createQueryBuilder();
        $qb->hydrate(true)->field('id')->in($keys);
        $result = $qb->getQuery()->execute();
        foreach($result as $document){
            $solrObject = $solrObjects[$document->getId()];
            $entity = new $class($document,$solrObject);
            $entity->setFacets($facets);
            $entities[] = $entity;
        }
        return $entities;
    }

    /**
     * Create a new instance of ResultConverter
     * @param   ServiceLocatorInterface $sl
     * @return  ResultConverter
     */
    static public function factory(ServiceLocatorInterface $sl)
    {
        return new static($sl);
    }
}