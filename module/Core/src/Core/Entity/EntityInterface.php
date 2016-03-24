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
 * Model interface
 */
interface EntityInterface
{

    /**
     * Checks, wether a property is not empty.
     *
     * @param string $property
     * @return bool
     */
    public function notEmpty($property);
}
