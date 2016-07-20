<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Entity;

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
class SolrJob extends Job
{
    /**
     * @var JobInterface
     */
    protected $document;

    /**
     * @var \SolrObject
     */
    protected $result;

    public function __construct(JobInterface $job,$solr)
    {
        $this->document = $job;
        $blacklist = ['getPublisher'];
        $this->importProperty($blacklist);
        $this->result = $solr;

        // handle date manually
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

    public function __get($property)
    {
        $result = $this->result;
        if(property_exists($result,$property)){
            $value = $result->$property;

            // we convert value to date time first
            // if the value is in date time format
            $value = Util::validateDate($value);

            return $value;
        }else{
            return call_user_func(array($this,'get'.$property));
        }
    }
}