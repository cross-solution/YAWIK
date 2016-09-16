<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Filter;


use Jobs\Entity\Location;
use Jobs\Entity\Job;
use Solr\Bridge\Util;
use Solr\Entity\JobProxy;
use SolrDisMaxQuery;
use SolrQuery;
use ArrayAccess;

/**
 * Class JobBoardPaginationQuery
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @package Solr\Filter
 */
class JobBoardPaginationQuery extends AbstractPaginationQuery
{

    /**
     * @inheritdoc
     */
    public function createQuery(array $params, SolrDisMaxQuery $query)
    {
        $search = isset($params['search']) ? trim($params['search']) : '';

        if (!empty($search)) {
            $q = \SolrUtils::escapeQueryChars($search);
        } else {
            $q = '*:*';
            $query->addSortField('datePublishStart', SolrQuery::ORDER_DESC);
        }

        $query->setQuery($q);
        $query->addFilterQuery('entityName:job');
        $query->addFilterQuery('isActive:1');
        $query->addField('*');
        
        if(isset($params['location'])){
            /* @var Location $location */
            $location = $params['location'];
            if(is_object($location->getCoordinates())){
                $coordinate = Util::convertLocationCoordinates($location);

                $query->addFilterQuery(
                    sprintf(
                        '{!parent which="entityName:job" childQuery="entityName:location"}{!geofilt pt=%s sfield=point d=%d score="kilometers"}',
                        $coordinate,
                        $params['d']
                    ));
                $query->addParam(
                    'locations.q',
                    sprintf(
                        'entityName:location AND {!terms f=_root_ v=$row.id} AND {!geofilt pt=%s sfield=point d=%s}',
                        $coordinate,
                        $params['d']
                    )); // join

                $query->addField('locations:[subquery]')
                      ->addField('distance:min(geodist(points,'.$coordinate.'))');

            }

            $query->addField('score');
        }
        
        // boost newest jobs
        $query->addParam('bf', 'recip(abs(ms(NOW/HOUR,datePublishStart)),3.16e-11,1,.1)');


        // adds facets into the result set.
        $query->setFacet(true);
        $query->addFacetField('regionList');
        $query->addFacetDateField('datePublishStart');

        // adds an additional 'highlights' section into the result set
        $query->setHighlight(true);
        $query->addHighlightField('title');

        return $query;
    }

    /**
     * @see \Solr\Filter\AbstractPaginationQuery::proxyFactory()
     */
    public function proxyFactory($entity, ArrayAccess $solrResult)
    {
        return new JobProxy($entity, $solrResult);
    }

    /**
     * @see \Solr\Filter\AbstractPaginationQuery::getRepositoryName()
     */
    public function getRepositoryName()
    {
        return 'Jobs/Job';
    }
}