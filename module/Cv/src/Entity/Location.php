<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Location.php */
namespace Cv\Entity;

use Core\Entity\AbstractLocation;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use GeoJson\GeoJson;

/**
 * Location of an Applicant
 *
 * @ODM\EmbeddedDocument
 * @ODM\Index(keys={"coordinates"="2dsphere"})
 *
 */
class Location extends AbstractLocation
{
}
