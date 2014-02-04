<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsContainer.php */ 
namespace Core\Entity;

use Settings\Entity\ModuleSettingsContainer;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Settings\Entity\InitializeAwareSettingsContainerInterface;

/**
 * @ODM\EmbeddedDocument
 */
class SettingsContainer extends ModuleSettingsContainer implements InitializeAwareSettingsContainerInterface
{
    
    /**
     * @ODM\EmbedOne(targetDocument="LocalizationSettings")
     */
    protected $localization;
    
    public function init()
    {
        $this->getLocalization();
    }
    
    public function getLocalization()
    {
        if (!$this->localization) {
            $this->localization = new LocalizationSettings();
        }
        return $this->localization;
    }
}

