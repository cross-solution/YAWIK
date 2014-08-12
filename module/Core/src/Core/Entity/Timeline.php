<?php
/**
 * YAWIK
 * Application configuration
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Any type of timeline
 * 
 * @ODM\EmbeddedDocument
 */
class Timeline extends AbstractEntity
{
}
