<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ModificationDateAwareEntityInterface.php */
namespace Core\Entity;

use DateTime;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

/**
 * Defines an entity which is aware of its creation and modification dates.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */
interface ModificationDateAwareEntityInterface
{
    /**
     * Sets the creation date.
     *
     * @param DateTime|LifecycleEventArgs|null $date
     * @return ModificationDateAwareEntityInterface
     * @throws \InvalidArgumentException
     */
    public function setDateCreated($date);
    
    /**
     * Gets the creation date.
     *
     * @return DateTime|null
     */
    public function getDateCreated();
    
    /**
     * Sets the modification date.
     *
     * @param DateTime|LifecycleEventArgs|string|null $date
     * @return ModificationDateAwareEntityInterface
     * @throws \InvalidArgumentException
     */
    public function setDateModified($date);
    
    /**
     * Gets the modification date.
     *
     * @return DateTime|null
     */
    public function getDateModified();
}
