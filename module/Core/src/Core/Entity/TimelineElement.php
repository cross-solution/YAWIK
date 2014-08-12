<?php
/**
 * YAWIK
 * Application configuration
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Any type of timeline
 * 
 * @ODM\EmbeddedDocument
 */
class TimelineElement extends AbstractEntity implements HistoryInterface
{
    /**
     * @ODM\Field(type="tz_date")
     */
    protected $date;
    
    /**
     * @return the $date
     */
    public function getDate ()
    {
        return $this->date;
    }

    /**
     * @param field_type $date
     * @return $this
     */
    public function setDate (\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
   
}