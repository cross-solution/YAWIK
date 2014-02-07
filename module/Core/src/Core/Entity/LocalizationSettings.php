<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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
     * @ODM\String
     */
    protected $language;
}

