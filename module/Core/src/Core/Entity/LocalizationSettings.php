<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** LocalizationSettings.php */
namespace Core\Entity;

use Settings\Entity\SettingsContainer as Container;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class LocalizationSettings extends Container
{
    /**
     * @ODM\Field(type="string")
     */
    protected $language;
    
    /**
     * @ODM\Field(type="string")
     */
    protected $timezone;
}
