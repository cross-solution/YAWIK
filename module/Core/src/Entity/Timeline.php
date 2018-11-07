<?php
/**
 * YAWIK
 * Application configuration
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Any type of timeline
 *
 * @author Anthonius Munthi <me@itstoni.com>
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
        $this->date = new \DateTime();
    }
    
    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
}
