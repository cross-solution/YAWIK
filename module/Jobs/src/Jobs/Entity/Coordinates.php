<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Coordinates.php */
namespace Jobs\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Coordinate of a job position
 *
 * @ODM\EmbeddedDocument
 *
 */
class Coordinates extends AbstractEntity implements CoordinatesInterface
{
    /**
     * Longitude of a job coordinate
     *
     * @ODM\String
     */
    protected $x;

    /**
     * Latitude of a job coordinate
     *
     * @ODM\String
     */
    protected $y;


    public function __construct()
    {

    }

    public function getX()
    {
        return $this->x;
    }

    public function setX($x)
    {
        $this->x = $x;
        return $this;
    }

    public function getY()
    {
        return $this->y;
    }

    public function setY($y)
    {
        $this->y = $y;
        return $this;
    }
}
