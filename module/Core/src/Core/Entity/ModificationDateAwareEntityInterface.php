<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ModificationDateAwareEntityInterface.php */ 
namespace Core\Entity;

use DateTime;

/**
 * Defines an entity which is aware of its creation and modification dates.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface ModificationDateAwareEntityInterface
{
    /**
     * Sets the creation date.
     * 
     * @param DateTime $date
     * @return self
     */
    public function setDateCreated(DateTime $date);
    
    /**
     * Gets the creation date.
     * 
     * @return DateTime
     */
    public function getDateCreated();
    
    /**
     * Sets the modification date.
     * 
     * @param DateTime|String $date
     * @return self
     */
    public function setDateModified($date);
    
    /**
     * Gets the modification date.
     * 
     * @return DateTime
     */
    public function getDateModified();
    
}

