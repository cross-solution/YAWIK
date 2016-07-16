<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** StatusInterface.php */
namespace Core\Entity;

/**
 * Application StatusInterface
 */
interface StatusInterface
{
    /**
     * Get an array of all available status names.
     *
     * @return string[]
     */
    public static function getStates();

    /**
     * Create a instance.
     *
     * @param string|null $name Status name.
     */
    public function __construct($name = null);

    /**
     * Check if this is a specific state.
     *
     * @param StatusInterface|string $name
     *
     * @return bool
     */
    public function is($name);

    /**
     * Converts an status entity into a string.
     *
     * @return string
     */
    public function __toString();
}
