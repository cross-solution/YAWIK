<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Filter;


use Doctrine\Common\Collections\ArrayCollection;
use Jobs\Entity\Location;
use Jobs\Entity\Job;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationName;
use Solr\Bridge\Util;

/**
 * Class JobBoardPaginationQuery
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Filter
 */
class JobBoardPaginationQuery extends AbstractPaginationQuery
{
    /**
     * @var array
     */
    protected $sortPropertiesMap = [
        'company' => 'companyName',
        'date'    => 'dateCreated',
    ];

    protected $propertiesMap = [
        'organizationName' => 'convertOrganizationName',
        'companyLogo'      => 'convertCompanyLogo',
        'locations'        => 'convertLocations'
    ];

    /**
     * @inheritdoc
     */
    public function createQuery(array $params, $query)
    {
        $search = isset($params['search']) ? $params['search']:'';

        if(!empty($search)){
            $q = \SolrUtils::escapeQueryChars($search);
        }else{
            $q = '*:*';
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
     * @inheritdoc
     */
    public function getEntityClass()
    {
        return Job::class;
    }

    /**
     * Convert organizationName result
     * @param Job       $ob
     * @param string    $value
     */
    public function convertOrganizationName($ob,$value)
    {
        if(!is_object($ob->getOrganization())){
            $ob->setOrganization(new Organization());
        }
        $orgName = new OrganizationName($value);
        $ob->getOrganization()->setOrganizationName($orgName);
    }

    /**
     * Convert companyLogo result
     * @param   Job     $ob
     * @param   mixed   $value
     */
    public function convertCompanyLogo($ob,$value)
    {
        if(!is_object($ob->getOrganization())){
            $ob->setOrganization(new Organization());
        }
        $exp    = explode('/',$value);
        $id     = $exp[3];
        $name   = isset($exp[4])?:null;
        $image = new OrganizationImage();
        $image->setId($id);
        $image->setName($name);
        $ob->getOrganization()->setImage($image);
    }

    /**
     * Convert locations result
     * @param   Job     $ob
     * @param   mixed   $value
     */
    public function convertLocations($ob,$value)
    {
        $locations = [];
        foreach($value->docs as $doc) {
            $locations[] = $doc->city;
        }
        $ob->setLocation(implode(', ', array_unique($locations)));
    }
}