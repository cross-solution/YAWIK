<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Status;

/**
 * Entities implementing this interface can be assigned a status.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,29
 */
interface StatusAwareEntityInterface
{
    /**
     * FQCN of the concrete status entity for this entity.
     *
     * @var string
     */
    const STATUS_ENTITY_CLASS = StatusInterface::class;

    /**
     * Set the state of this entity.
     *
     * * If a string is passed, {@łink STATUS_ENTITY_CLASS} is used
     *   to create a new status entity.
     * * Checks a provided object if it is an instance of
     *   {@link STATUS_ENTITY_CLASS}
     *
     * @param string|StatusInterface $state
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setStatus($state);

    /**
     * Get the state of this entity.
     *
     * @return StatusInterface
     */
    public function getStatus();

    /**
     * Has this entity a (particular) state?
     *
     * Returns true, if the assigned status is equal to $state.
     * If $state is null, returns true, if a status is assigned.
     *
     * @param null|string|StatusInterface $state
     *
     * @return bool
     */
    public function hasStatus($state = null);
}
