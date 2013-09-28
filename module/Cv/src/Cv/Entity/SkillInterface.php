<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Cv\Entity;

use Core\Entity\EntityInterface;

interface SkillInterface extends EntityInterface
{
	/*
	 * name of the language de,en,fr
	 */
    public function setNativeLanguages($nativeLanguages);
    public function getNativeLanguages();
    
    /*
     * 
     */
    public function setLanguageSkills($languageSkills);
    public function getLanguageSkills();
    
    /*
     * listening, speeking, writing
     */
    public function setComputerSkills($computerSkills);
    public function getComputerSkills();
    
    
}