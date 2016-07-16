<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

/**
 * Status aware entity interface.
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
interface StatusAwareEntityInterface 
{
    /**
     * Sets the status.
     *
     * Should create a status entity, if a string is passed.
     *
     * @param StatusInterface|string $status
     *
     * @return self
     */
    public function setStatus($status);

    /**
     * Gets the status.
     *
     * @return StatusInterface|null
     */
    public function getStatus();

    /**
     * Does this entity has a specific status?
     *
     * @param StatusInterface|string $status
     *
     * @return bool
     */
    public function hasStatus($status);

}