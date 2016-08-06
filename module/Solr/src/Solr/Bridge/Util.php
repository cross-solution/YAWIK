<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;

use Jobs\Entity\Location;

/**
 * Static utility to do conversion
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since  0.26
 * @package Solr\Bridge
 */
class Util
{
    /**
     * Convert Location coordinate into acceptable solr document format
     * @param Location $location
     * @return string
     */
    static public function convertLocationCoordinates(Location $location)
    {
        $coordinates = $location->getCoordinates()->getCoordinates();
        $coordinate = doubleval($coordinates[0]).'%'.doubleval($coordinates[1]);
        $coordinate = strtr($coordinate,[
            '%'=>',',
            ','=>'.'
        ]);
        return $coordinate;
    }

    /**
     * Convert date time into acceptable solr document format
     * 
     * @param \DateTime $date
     * @return string
     */
    static public function convertDateTime(\DateTime $date)
    {
        return $date->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT);
    }

    /**
     * Convert date formatted string into a DateTime object
     *
     * @param   string  $value
     * @return  \DateTime|string
     */
    static public function validateDate($value)
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
}