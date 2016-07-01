<?php
/**
 * YAWIK
 *
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
 * @package Solr\Bridge
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
        foreach($response['response']['docs'] as $doc){
            $ob = new $class();
            $properties = $doc->getPropertyNames();
            foreach($properties as $name){
                $setter = 'set'.$name;
                $value = $doc->$name;
                $value = $this->validateDate($value);
                if(method_exists($ob,$setter)){
                    call_user_func(array($ob,$setter),$value);
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