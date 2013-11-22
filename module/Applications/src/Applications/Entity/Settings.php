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
    
    public function setMailInvitationText($option) {
        $this->mailInvitationText = $option;
        return $this;
    } 
    
    
    public function setMailConfirmationText($option) {
        $this->mailConfirmationText = $option;
        return $this;
    } 
    
    
    public function setMailRejectionText($option) {
        $this->mailRejectionText = $option;
        return $this;
    }
    
}
