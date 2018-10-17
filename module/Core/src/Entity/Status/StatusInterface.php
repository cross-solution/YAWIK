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
 * Interface for a status entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
interface StatusInterface
{
    /**
     * Get all available states.
     *
     * @return string[]
     */
    public static function getStates();

    /**
     * Creates a status.
     *
     * @param string $state
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($state);

    /**
     * String representation.
     *
     * Most likely the state name.
     *
     * @return string
     */
    public function __toString();

    /**
     * Check state.
     *
     * Returns true, if this state equals the given $state.
     * $state can be a string or any object which can be casted to a string.
     *
     * Even so, it is possible to provide any 'stringable' object, it does
     * only make sense to pass in instances of the same class.
     *
     * @param string|object $state
     *
     * @return bool
     */
    public function is($state);
}
