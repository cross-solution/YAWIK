<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Settings\Entity;
use Core\Entity\EntityResolverStrategyInterface;

class Settings extends SettingsAbstract {
    
    /**
     * 
     * @return date
     */
    public function getLanguage() {
        return $this->language;
    }
    
    public function setLanguage($name) {
        $this->language = $name;
        return;
    }
}
