<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

/**
 * This interface enables an element to be disabled (ignored).
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface DisableCapableInterface
{

    /**
     * Sets if this element is capable of beeing disabled.
     *
     * if set to <b>true</b>, this element can be disabled.
     * if set to <b>false</b>, this element cannot be disabled, even if the containing form
     * tries to do so.
     *
     * @param boolean $flag
     *
     * @return self
     */
    public function setIsDisableCapable($flag);

    /**
     * Gets if this element is capable of beeing disabled.
     *
     * @return boolean
     */
    public function isDisableCapable();
}
