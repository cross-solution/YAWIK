<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** CoordinateInterface.php */
namespace Jobs\Entity;

use Core\Entity\EntityInterface;

/**
 * Interface CoordinatesInterface
 *
 * @package Jobs\Entity
 */
interface CoordinatesInterface extends EntityInterface
{
    /**
     * Sets the longitude of a coordinate
     *
     * @param $x
     * @return mixed
     */
    public function setX($x);

    /**
     * Gets the logitude of a coordinate
     *
     * @return mixed
     */
    public function getX();

    /**
     * Gets the latitude of a location
     *
     * @return mixed
     */
    public function getY();

    /**
     * Sets the latitude of a location
     *
     * @param $y
     * @return mixed
     */
    public function setY($y);
}

