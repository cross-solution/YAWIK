<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Cv\Entity;

use Core\Entity\AbstractEntity;

class Skill extends AbstractEntity
{
    
    protected $nativeLanguages;
    protected $languageSkills;
    protected $computerSkills;
    
    public function setNativeLanguages($nativeLanguages)
    {
        $this->nativeLanguages = $nativeLanguages;
        return $this;
    }
    
    public function getNativeLanguages()
    {
        return $this->nativeLanguages;
    }
    
    public function setLanguageSkills($languageSkills)
    {
        $this->languageSkills = $languageSkills;
        return $this;
    }
    
    public function getLanguageSkills()
    {
        return $this->languageSkills;
    }
    
    public function setComputerSkills($computerSkills)
    {
    	$this->computerSkills = $computerSkills;
    	return $this;
    }
    
    public function getComputerSkills()
    {
    	return $this->computerSkills;
    }
}