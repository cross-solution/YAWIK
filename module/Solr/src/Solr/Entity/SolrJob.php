<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Entity;

use Jobs\Entity\Feature\FacetsProviderInterface;
use Jobs\Entity\Job;
use Jobs\Entity\JobInterface;
use Solr\Bridge\Util;

/**
 * Class Job
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Entity
 *
 */
class SolrJob extends Job implements FacetsProviderInterface
{
    /**
     * @var JobInterface
     */
    protected $document;

    /**
     * @var \SolrObject
     */
    protected $result;

    /**
     * @var array
     */
    protected $facets;

    public function __construct(JobInterface $job,$solr)
    {
        $this->document = $job;
        $blacklist = ['getPublisher'];
        $this->importProperty($blacklist);
        $this->result = $solr;
    }

    public function importProperty($blacklist)
    {
        $document = $this->document;
        $methods = get_class_methods($document);
        foreach($methods as $method){
            if(0 === strpos($method,'get') && !in_array($method,$blacklist)){
                $value = call_user_func(array($document,$method));
                if(!is_null($value)){
                    $setter = 'set'.substr($method,3);
                    call_user_func(array($this,$setter),$value);
                }
            }
        }
    }

    /**
     * @param $facets
     * @return $this
     */
    public function setFacets($facets)
    {
        $this->facets = $facets;
        return $this;
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }
}