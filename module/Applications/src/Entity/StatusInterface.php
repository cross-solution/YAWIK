<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** StatusInterface.php */
namespace Applications\Entity;

use Core\Entity\EntityInterface;

/**
 * Application StatusInterface
 */
interface StatusInterface extends EntityInterface
{
    /**
     * A new application has been received
     */
    const INCOMING  = 'incoming';

    /**
     * An acknowledgement of receipt has been sent
     */
    const CONFIRMED = 'confirmed';

    /**
     * application was accepted to be processed by a recruiter
     */
    const ACCEPTED = 'accepted';

    /**
     * An applicant ist invited to in interview
     */
    const INVITED   = 'invited';

    /**
     * The applicant has been canceled
     */
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

    /**
     * Gets an array of states
     */
    public function getStates();
}
