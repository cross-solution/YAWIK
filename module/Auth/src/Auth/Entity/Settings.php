<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Entity;
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
        return;
    }
    
    public function getMailText() {
        return $this->mailText;
    }
    
    public function setMailText($option) {
        $this->mailText = $option;
        return;
    }
}
