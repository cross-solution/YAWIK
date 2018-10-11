<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Entity\Geometry;

use GeoJson\Geometry\Point as GeoJsonPoint;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Wrapper class for GeoJson\Geometry\Point which allows to store a point
 * in the mongo db.
 *
 * @ODM\EmbeddedDocument
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Point extends  GeoJsonPoint
{

    /**
     * GeoJson type
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $type = "Point";

    /**
     * Coordinates
     *
     * @var array
     * @ODM\Field(type="collection")
     */
    protected $coordinates;
}