<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;

use Core\Repository\RepositoryService;
use Solr\Filter\AbstractPaginationQuery;
use Zend\ServiceManager\ServiceLocatorInterface;
use ArrayAccess;
use InvalidArgumentException;

/**
 * Class ResultConverter
 *
 * Convert SOLR query result into Doctrine ODM Entity
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @package Solr\Bridge
 * @since 0.26
 */
class ResultConverter
{

    /**
     * @var RepositoryService
     */
    protected $repositories;

    /**
     * @param RepositoryService $repositories
     */
    public function __construct(RepositoryService $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Convert result into entities
     *
     * @param AbstractPaginationQuery $filter
     * @param ArrayAccess $response
     * @return array Array of entities
     * @throws InvalidArgumentException
     */
    public function convert(AbstractPaginationQuery $filter, ArrayAccess $response)
    {
        $entities = [];
        $ids = [];
        $return = [];
        $emptyEntity = null;

        if (!isset($response['response'])
            || !isset($response['response']['docs'])
            || !is_array($response['response']['docs']))
        {
            throw new InvalidArgumentException('invalid response');
        }
        
        foreach ($response['response']['docs'] as $doc) {
            $ids[] = $doc->id;
        }

        // fetch entities with given IDs
        $repository = $this->repositories->get($filter->getRepositoryName());
        $qb = $repository->createQueryBuilder() /* @var $repository \Core\Repository\AbstractRepository */
            ->field('id')
            ->in($ids);
        $result = $qb->getQuery()->execute();
        
        foreach ($result as $document) {
            $entities[$document->getId()] = $document;
        }
        
        // iterate over Solr response to preserve sorting
        foreach ($response['response']['docs'] as $doc) {
            // check if entity exists
            if (isset($entities[$doc->id])) {
                // use found entity
                $entity = $entities[$doc->id];
            } else {
                if (!isset($emptyEntity)) {
                    // create empty entity
                    $emptyEntity = $repository->create();
                }
                
                // use empty entity
                $entity = $emptyEntity;
            }
            
            $return[] = $filter->proxyFactory($entity, $doc);
        }
        
        return $return;
    }

    /**
     * Create a new instance of ResultConverter
     * @param   ServiceLocatorInterface $sl
     * @return  ResultConverter
     */
    static public function factory(ServiceLocatorInterface $sl)
    {
        return new static($sl->get('repositories'));
    }
}