<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications\Entity;
use Settings\Entity\SettingsAbstract;
use Core\Entity\EntityResolverStrategyInterface;

class Settings extends SettingsAbstract {
    
    /**
     * 
     * @return email
     */
    public function getMailAccess() {
        return $this->mailAccess;
    }
    
    public function setMailAccess($option) {
        $this->mailAccess = $option;
        return $this;
    }
    
    public function getMailText() {
        return $this->mailText;
    }
    
    public function setMailText($option) {
        $this->metMailText = $option;
        return $this;
    }
    
   
    public function setMailAccessText($option) {
        $this->mailAccessText = $option;
        return $this;
    } 
    
    public function setMailInvitation($option) {
        $this->mailInvitation = $option;
        return $this;
    } 
    
    public function setMailInvitationText($option) {
        $this->mailInvitationText = $option;
        return $this;
    } 
    
    public function setMailAcknowledgement($option) {
        $this->mailAcknowledgement = $option;
        return $this;
    } 
    
    public function setMailAcknowledgementText($option) {
        $this->mailAcknowledgementText = $option;
        return $this;
    } 
    
    public function setMailDecline($option) {
        $this->mailDecline = $option;
        return $this;
    } 
    
    public function setMailDeclineText($option) {
        $this->mailDeclineText = $option;
        return $this;
    }
    
}
