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
class Timeline extends AbstractEntity 
{
    /**
     * @ODM\Field(type="tz_date")
     */
    protected $date;
    
    public function __construct()
    {
        $this->getDate();
    }
    
    /**
     * @return $date
     */
    public function getDate ()
    {
        if (!$this->date) {
            $this->setDate(new \DateTime());
        }
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate (\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
   
}