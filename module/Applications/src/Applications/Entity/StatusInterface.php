<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** StatusInterface.php */ 
namespace Applications\Entity;

use Core\Entity\EntityInterface;

/**
 * Application StatusInterface
 */
interface StatusInterface extends EntityInterface
{
    const INCOMING  = 'incoming';
    const CONFIRMED = 'confirmed';
    const INVITED   = 'invited';
    const REJECTED  = 'rejected';
    
    public function __construct($status = self::INCOMING);

    /**
     * Gets the Name of an application state.
     */
    public function getName();
    
    /**
     * Gets an integer of an application state.
     */
    public function getOrder();
    
    /**
     * Converts an status entity into a string
     */
    public function __toString();
}

