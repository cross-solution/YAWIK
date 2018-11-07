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
 * Trait implementing DraftableEntityInterface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
trait DraftableEntityTrait
{
    /**
     * The current draft state of this entity.
     *
     * @ODM\Field(type="boolean")
     * @var bool
     */
    protected $isDraft = true;

    public function isDraft()
    {
        return $this->isDraft;
    }
    
    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;

        return $this;
    }
}
