<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** StatusInterface.php */
namespace Jobs\Entity;

use Core\Entity\EntityInterface;

/**
 * Jobs StatusInterface
 * @see http://www.gliffy.com/go/publish/6822105
 */
interface StatusInterface extends EntityInterface
{
    /**
     * A new job was created.
     */
    const CREATED =  /*@translate*/ 'created';

    /**
     * A new job is waiting for approval
     */
    const WAITING_FOR_APPROVAL = /*@translate*/ 'waiting for approval';

    /**
     * A job was rejected to be published.
     */
    const REJECTED = /*@translate*/ 'rejected';

    /**
     * A Job is accepted an is going to be published
     */
    const PUBLISH  = /*@translate*/ 'publish';

    /**
     * A Job is online
     */
    const ACTIVE  = /*@translate*/ 'active';

    /**
     * A job was is set inactive
     */
    const INACTIVE = /*@translate*/ 'inactive';

    /**
     * A job was expired
     */
    const EXPIRED  = /*@translate*/ 'expired';
    
    public function __construct($status = self::CREATED);

    /**
     * Gets the Name of the job state.
     */
    public function getName();
    
    /**
     * Gets an integer of the job state.
     */
    public function getOrder();
    
    /**
     * Converts an status entity into a string
     */
    public function __toString();
}
