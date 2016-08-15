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
        $propertiesMap = $filter->getPropertiesMap();
        $class = $filter->getEntityClass();
        $entities = [];
        foreach($response['response']['docs'] as $doc){ /* @var $doc \SolrObject  */
            $ob = new $class();
            $properties = $doc->getPropertyNames();
            foreach($properties as $name){
                $setter = 'set'.$name;
                $value = $doc->$name;
                $value = $this->validateDate($value);
                if ($value instanceof \SolrObject) {
                    if ($name == 'locations') {
                        $this->useGeoLocation=true;
                    }
                    $this->handleMappedProperty($propertiesMap[$name],$ob,$value);
                } elseif (method_exists($ob,$setter) && !$value instanceof \SolrObject){
                    if ($name != 'location') {
                        call_user_func(array($ob, $setter), $value);
                    }elseif (!$this->useGeoLocation) {
                        call_user_func(array($ob, $setter), $value);
                    }
                }elseif(isset($propertiesMap[$name])){
                    $this->handleMappedProperty($propertiesMap[$name],$ob,$value);
                }

            }
            $entities[] = $ob;
        }

        return $entities;
    }

    /**
     * Handles mapped property defined by query filter
     * 
     * @param $property
     * @param $object
     * @param $value
     */
    public function handleMappedProperty($property,$object,$value)
    {
        $callback = array($this->filter,$property);
        if(is_callable($callback)){
            call_user_func($callback,$object,$value);
        }
    }

    /**
     * Convert date formatted string into a DateTime object
     *
     * @param   string  $value
     * @return  \DateTime|string
     */
    public function validateDate($value)
    {
        if ($value instanceof \SolrObject || is_array($value)){
            return $value;
        }
        $value = trim($value);
        $date = \DateTime::createFromFormat(Manager::SOLR_DATE_FORMAT,$value);
        $check = $date && ($date->format(Manager::SOLR_DATE_FORMAT) === $value);
        if($check){
            return $date;
        }else{
            return $value;
        }
    }

    /**
     * Create a new instance of ResultConverter
     * @param   ServiceLocatorInterface $sl
     * @return  ResultConverter
     */
    static public function factory(ServiceLocatorInterface $sl)
    {
        return new static();
    }
}