<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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

    /**
     * Initialize the settings container
     */
    public function init()
    {
        $this->getLocalization();
    }

    /**
     * Get localization settings
     *
     * @return LocalizationSettings
     */
    public function getLocalization()
    {
        if (!$this->localization) {
            $this->localization = new LocalizationSettings();
        }
        return $this->localization;
    }
}
