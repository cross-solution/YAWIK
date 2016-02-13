<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core forms */
namespace Core\Form;

/**
 * Enables form elements to provide a view partial when being rendered.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface ViewPartialProviderInterface
{
    /**
     * Sets the view partial name.
     *
     * @param String $partial
     * @return self
     */
    public function setViewPartial($partial);
    
    /**
     * Gets the view partial name.
     *
     * @return string
     */
    public function getViewPartial();
}
