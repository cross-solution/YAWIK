<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
class Settings extends ModuleSettingsContainer
{
    
    /**
     * send mail to the recruiter
     *
     * @ODM\Boolean
     */
    protected $mailAccess = false;
    
    /**
     * send BlindCarbonCopy to owner(?)
     *
     * @ODM\Boolean
     */
    protected $mailBCC = false;

    
    /**
     * @todo document this
     *
     * @ODM\Boolean
     */
    protected $formDisplaySkills = false;
    

    /**
     * Confirm application immediately after submitting.
     *
     * @ODM\Boolean
     */
    protected $autoConfirmMail = false;
    
    /**
     * Mail text, which informs the recruiter about an incoming application
     *
     * @ODM\String
     */
    protected $mailAccessText;
    
    /**
     * Mail text for inviting an applicant. Mail is sent to the applicant
     *
     * @ODM\String
     */
    protected $mailInvitationText;
    
    
    /**
     * Mail text for confirming an application-
     *
     * @ODM\String
     */
    protected $mailConfirmationText;
    
    /**
     * Mail text for rejecting an application
     *
     * @ODM\String
     */
    protected $mailRejectionText;

    /**
     * Disabled elements of the apply form.
     *
     * @ODM\EmbedOne(targetDocument="\Settings\Entity\DisableElementsCapableFormSettings")
     * @var \Settings\Entity\DisableElementsCapableFormSettings
     */
    protected $applyFormSettings;

    /**
     * @param $mailAccess
     * @return $this
     */
    public function setMailAccess($mailAccess)
    {
        $this->mailAccess = (bool) $mailAccess;
        return $this;
    }

    /**
     * @param $formDisplaySkills
     * @return $this
     */
    public function setFormDisplaySkills($formDisplaySkills)
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
