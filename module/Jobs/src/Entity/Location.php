<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** Location.php */
namespace Jobs\Entity;

use Core\Entity\AbstractLocation;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use GeoJson\GeoJson;

/**
 * Location of a job position
 *
 * @ODM\EmbeddedDocument
 * @ODM\Index(keys={"coordinates"="2dsphere"})
 *
 */
class Location extends AbstractLocation
{
}
