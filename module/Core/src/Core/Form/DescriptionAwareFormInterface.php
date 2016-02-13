<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Form;

/**
 * Marks a form to be descriptions aware.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface DescriptionAwareFormInterface
{
    /**
     * Sets the descriptions enabled flag
     * @param boolean $flag
     * @return self
     */
    public function setIsDescriptionsEnabled($flag);
    
    /**
     * Returns the state of description awareness.
     *
     * @return boolean
     */
    public function isDescriptionsEnabled();
    
    /**
     * Sets the description for this form..
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description);
}
