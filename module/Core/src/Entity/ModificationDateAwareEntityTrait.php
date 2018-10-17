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
use InvalidArgumentException;
use Exception;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

/**
 * Implementation stub for DateAware entities.
 *
 * Since this class uses Doctrines' PrePersist and PreUpdate annotations,
 * you must annotate any class using this trait with the __@HasLifecycleCallbacks__
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
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
     * @see \Core\Entity\ModificationDateAwareEntityInterface::setDateCreated()
     * @ODM\PrePersist
     */
    public function setDateCreated($dateCreated = null)
    {
        if (!isset($dateCreated) || $dateCreated instanceof LifecycleEventArgs) {
            $dateCreated = new DateTime();
        }
        if (!$dateCreated instanceof DateTime) {
            throw new InvalidArgumentException(sprintf('$dateCreated has to be null, %s or %s', DateTime::class, LifecycleEventArgs::class));
        }
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @see \Core\Entity\ModificationDateAwareEntityInterface::getDateModified()
     * @ODM\PrePersist
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @see \Core\Entity\ModificationDateAwareEntityInterface::getDateModified()
     * @ODM\PrePersist
     * @ODM\PreUpdate
     */
    public function setDateModified($dateModified = null)
    {
        if (!isset($dateModified) || $dateModified instanceof LifecycleEventArgs) {
            $dateModified = new DateTime();
        }
        if (is_string($dateModified)) {
            try {
                $dateModified = new DateTime($dateModified);
            } catch (Exception $e) {
                throw new InvalidArgumentException('Invalid date string', 0, $e);
            }
        }
        if (!$dateModified instanceof DateTime) {
            throw new InvalidArgumentException(sprintf('$dateModified has to be null, string, %s or %s', DateTime::class, LifecycleEventArgs::class));
        }
        $this->dateModified = $dateModified;
        return $this;
    }
}
