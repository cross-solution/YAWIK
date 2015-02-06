<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core Entities */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use DateTime;

/**
 * Abstract Identifiable Modification Date Aware Entity
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\MappedSuperclass @ODM\HasLifecycleCallbacks
 */
abstract class AbstractIdentifiableModificationDateAwareEntity 
    extends    AbstractIdentifiableEntity 
    implements ModificationDateAwareEntityInterface
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
     * @var DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateModified;
    
    
    /**
     * {@inheritDoc}
     */
    public function getDateCreated ()
    {
        return $this->dateCreated;
    }

    /**
     * {@inheritDoc}
     * @ODM\PrePersist
     */
    public function setDateCreated (DateTime $dateCreated = Null)
    {
        if (!isset($dateCreated)) {
            $dateCreated = new DateTime();
        }
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
	 * {@inheritDoc}
     */
    public function getDateModified ()
    {
        return $this->dateModified;
    }

    /**
     * {@inheritDoc}
     *  @ODM\PreUpdate
     */
    public function setDateModified ($dateModified = Null)
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