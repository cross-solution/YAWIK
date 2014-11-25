<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Entity;



use Settings\Entity\DisableElementsCapableFormSettings;
use Settings\Entity\ModuleSettingsContainer;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * General Settings for the application module.
 *
 * @ODM\EmbeddedDocument
 */
class Settings extends ModuleSettingsContainer {
    
    /**
     * 
     * @ODM\Boolean
     * formLabel Send mail
     */
    protected $mailAccess = false;
    
    /**
     * 
     * @ODM\Boolean
     * formLabel Send BlindCarbonCopy to owner
     */
    protected $mailBCC = false;
    
    
    /**
     *
     * @ODM\Boolean
     * formLabel Send mail
     */
    protected $formDisplaySkills = false;
    

    /**
     *
     * @ODM\Boolean
     * formLabel Confirm application immidiatly after submitting
     */
    protected $autoConfirmMail = false;
    
    /**
     *
     * @ODM\String
     * formLabel Send mail
     */
    protected $mailAccessText;
    
    /**
     *
     * @ODM\String
     * formLabel Send mail
     */
    protected $mailInvitationText;
    
    
    /**
     *
     * @ODM\String
     * formLabel Send mail
     */
    protected $mailConfirmationText;
    
    /**
     *
     * @ODM\String
     * formLabel Send mail
     */
    protected $mailRejectionText;

    /**
     * Disabled elements of the apply form.
     *
     * @ODM\EmbedOne(targetDocument="\Settings\Entity\DisableElementsCapableFormSettings")
     * @var \Settings\Entity\DisableElementsCapableFormSettings
     */
    protected $applyFormSettings;

    public function setMailAccess($mailAccess)
    {
        $this->mailAccess = (bool) $mailAccess;
        return $this;
    }
    
	public function setFormDisplaySkills ($formDisplaySkills)
    {
        $this->formDisplaySkills = (bool) $formDisplaySkills;
        return $this;
    }

    /**
     * Gets the disabled apply form elements settings.
     *
     * @return DisableElementsCapableFormSettings
     */
    public function getApplyFormSettings()
    {
        if (!$this->applyFormSettings) {
            $settings = new DisableElementsCapableFormSettings();
            $settings->setForm('Applications/Apply');
            $this->applyFormSettings = $settings;
        }

        return $this->applyFormSettings;
    }

}
