<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Repository\DoctrineMongoODM\Types;

use Doctrine\ODM\MongoDB\Types\Type;

/**
 * This is a mapping type for DoctrineMongoODM.
 *
 * It will convert \DateTime objects to an array representation
 * with a \MongoDate object and the Timezone.
 *
 * This works around the fact, that \MongoDate is NOT
 * timezone aware.
 *
 * @version 1.1
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class TimezoneAwareDate extends Type
{
    
    /**
     * Converts a \DateTime object to MongoDB representation.
     *
     * If <code>$value</code> is <i>NULL</i> (or not a \DateTime object),
     * nothing is done and <i>NULL</i> returned.
     *
     * @internal
     *      Resulting value is an array:
     *      <pre>
     *          array(
     *              'date' => \MongoDate(\DateTime::timestamp),
     *              'tz'   => TimeZone-Identifier (e.g. "Europe/Berlin")
     *          )
     *      </pre>
     *
     * @param \DateTime|null $value
     * @return null|array
     * @see \Doctrine\ODM\MongoDB\Types\Type::convertToDatabaseValue()
     */
    public function convertToDatabaseValue($value)
    {
        if (!$value instanceof \DateTime) {
            return null;
        }
        
        $timezone  = $value->getTimezone()->getName();
        $timestamp = $value->getTimestamp();
        $date      = new \MongoDate($timestamp);
        
        return array(
            'date' => $date,
            'tz'   => $timezone,
        );
    }
    
    /**
     * PHP code to be used in DoctrineMongoODM-Hydrators.
     *
     * @return string
     * @see convertToDatabaseValue()
     */
    public function closureToDatabase()
    {
        return '
            /* CODE FROM: ' . __METHOD__ . ' */
            if (!$value instanceOf \DateTime) return null;
            $return = array(
                "date" => new \MongoDate($value->getTimestamp()), 
                "tz" => $value->getTimestamp()->getName()
            );
            /* ---- */';
    }
    
    /**
     * Converts a TimezoneAwareDate array to a \DateTime Object.
     *
     * Returns <i>NULL</i>, if <code>$value</code> is an unknown format.
     *
     * @param array $value
     * @return NULL|\DateTime
     */
    public function convertToPhpValue($value)
    {
        if (!is_array($value)
            || !isset($value['date'])
            || !$value['date'] instanceof \MongoDate
            || !isset($value['tz'])
        ) {
            return null;
        }
        
        $timestamp = $value['date']->sec;
        $date = new \DateTime('@'.$timestamp);
        $date->setTimezone(new \DateTimeZone($value['tz']));
        return $date;
    }
    
    /**
     * PHP code to be used in DoctrineMongoODM-Hydrators.
     *
     * @return string
     * @see convertToPhpValue()
     */
    public function closureToPhp()
    {
        return '
            /* CODE FROM: ' . __METHOD__ . ' */
            if (!is_array($value) 
                || !isset($value["date"]) 
                || !$value["date"] instanceOf \MongoDate
                || !isset($value["tz"])
            ) {
                $return = null;
            } else {
                $date = new \DateTime("@".$value["date"]->sec);
                $date->setTimezone(new \DateTimeZone($value["tz"]));
                $return = $date;
            }
            /* ---- */
            ';
    }
}
