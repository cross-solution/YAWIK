<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
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
     */
    public function setDateCreated (DateTime $dateCreated)
    {
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
     */
    public function setDateModified ($dateModified)
    {
        if (is_string($dateModified)) {
            $dateModified = new DateTime($dateModified);
        }
        $this->dateModified = $dateModified;
        return $this;
    }

    /**
     * @ODM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setDateModified(new DateTime());
    }
    
    /**
     * @ODM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateCreated(new DateTime());
    }
    
}