<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications\Entity;



use Settings\Entity\ModuleSettingsContainer;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Settings extends ModuleSettingsContainer {
    
    /**
     * 
     * @ODM\Boolean
     * @formLabel Send mail
     */
    protected $mailAccess = false;
    
    /**
     *
     * @ODM\Boolean
     * @formLabel Send mail
     */
    protected $formDisplaySkills = false;
    

    /**
     *
     * @ODM\String
     * @formLabel Send mail
     */
    protected $mailText;
    
    /**
     *
     * @ODM\String
     * @formLabel Send mail
     */
    protected $mailAccessText;
    
    /**
     *
     * @ODM\String
     * @formLabel Send mail
     */
    protected $mailInvitationText;
    
    
    /**
     *
     * @ODM\String
     * @formLabel Send mail
     */
    protected $mailConfirmationText;
    
    /**
     *
     * @ODM\String
     * @formLabel Send mail
     */
    protected $mailRejectionText;
    
	public function getMailAccess ()
    {
        return $this->mailAccess;
    }

	public function setMailAccess ($mailAccess)
    {
        $this->mailAccess = $mailAccess;
        return $this;
    }

	public function getFormDisplaySkills ()
    {
        return $this->formDisplaySkills;
    }

	public function setFormDisplaySkills ($formDisplaySkills)
    {
        $this->formDisplaySkills = $formDisplaySkills;
        return $this;
    }

	public function getMailText ()
    {
        return $this->mailText;
    }

	public function setMailText ($mailText)
    {
        $this->mailText = $mailText;
        return $this;
    }

	public function getMailAccessText ()
    {
        return $this->mailAccessText;
    }

	public function setMailAccessText ($mailAccessText)
    {
        $this->mailAccessText = $mailAccessText;
        return $this;
    }

	public function getMailInvitationText ()
    {
        return $this->mailInvitationText;
    }

	public function setMailInvitationText ($mailInvitationText)
    {
        $this->mailInvitationText = $mailInvitationText;
        return $this;
    }

	public function getMailConfirmationText ()
    {
        return $this->mailConfirmationText;
    }

	public function setMailConfirmationText ($mailConfirmationText)
    {
        $this->mailConfirmationText = $mailConfirmationText;
        return $this;
    }

	public function getMailRejectionText ()
    {
        return $this->mailRejectionText;
    }

	public function setMailRejectionText ($mailRejectionText)
    {
        $this->mailRejectionText = $mailRejectionText;
        return $this;
    }
    
    public function getMailBCC() {
        return $this->mailBCC;
    }
    
    public function setMailBCC($option) {
        $this->mailBCC = $option;
        return $this;
    }
    
}
