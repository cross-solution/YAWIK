<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use DateTime;

/**
 * Implementation stub for DateAware entities.
 *
 * Since this class uses Doctrines' PrePersist and PreUpdate annotations,
 * you must annotate any class using this trait with the __@HasLifecycleCallbacks__
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @see \Core\Entity\ModificationDateAwareEntityInterface
 */
trait ModificationDateAwareEntityTrait
{
    /**
     * Creation date.
     *
     * @var DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateCreated;
    
    /**
     * Modification date.
     *
     * @var DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateModified;
    
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     *
     * @ODM\PrePersist
     */
    public function setDateCreated(DateTime $dateCreated = null)
    {
        if (!isset($dateCreated)) {
            $dateCreated = new DateTime();
        }
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     *
     *  @ODM\PreUpdate
     */
    public function setDateModified($dateModified = null)
    {
        if (!isset($dateModified)) {
            $dateModified = new DateTime();
        }
        if (is_string($dateModified)) {
            $dateModified = new DateTime($dateModified);
        }
        $this->dateModified = $dateModified;
        return $this;
    }
}
