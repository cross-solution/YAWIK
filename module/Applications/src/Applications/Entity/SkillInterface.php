<?php

namespace Applications\Entity;

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