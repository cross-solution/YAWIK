<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Entity;

use Settings\Entity\DisableElementsCapableFormSettings;
use Settings\Entity\ModuleSettingsContainer;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * General Settings for the application module.
 *
 * @method bool getMailBCC()
 * @method string getMailConfirmationText()
 *
 * @ODM\EmbeddedDocument
 */
class Settings extends ModuleSettingsContainer implements SettingsInterface
{
    
    /**
     * send mail to the recruiter
     *
     * @ODM\Field(type="boolean")
     */
    protected $mailAccess = false;
    
    /**
     * send BlindCarbonCopy to organization admin
     *
     * @ODM\Field(type="boolean")
     */
    protected $mailBCC = false;

    
    /**
     * @todo document this
     *
     * @ODM\Field(type="boolean")
     */
    protected $formDisplaySkills = false;
    

    /**
     * Confirm application immediately after submitting.
     *
     * @ODM\Field(type="boolean")
     */
    protected $autoConfirmMail = false;
    
    /**
     * Mail text, which informs the recruiter about an incoming application
     *
     * @ODM\Field(type="string")
     */
    protected $mailAccessText;
    
    /**
     * Mail text for inviting an applicant. Mail is sent to the applicant
     *
     * @ODM\Field(type="string")
     */
    protected $mailInvitationText;

    /**
     * Mail text for accepting an applicant. Mail is sent to the recruiter
     *
     * @ODM\Field(type="string")
     */
    protected $mailAcceptedText;
    
    
    /**
     * Mail text for confirming an application-
     *
     * @ODM\Field(type="string")
     */
    protected $mailConfirmationText;
    
    /**
     * Mail text for rejecting an application
     *
     * @ODM\Field(type="string")
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
     * @return bool
     */
    public function getMailAccess()
    {
        return $this->mailAccess;
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

    /**
     * @return bool
     */
    public function getAutoConfirmMail(){
        return $this->autoConfirmMail;
    }
}
