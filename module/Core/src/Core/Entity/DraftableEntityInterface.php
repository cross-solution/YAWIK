<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core models */
namespace Core\Entity;

/**
 * Draftable Model interface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface DraftableEntityInterface
{

    /**
     * Gets the flag indicating the draft state.
     *
     * @return bool
     */
    public function isDraft();
    
    /**
     * Sets the flag indicating the draft state.
     *
     * @param boolean $flag
     * @return DraftableEntityInterface
     */
    public function setIsDraft($flag);
}
