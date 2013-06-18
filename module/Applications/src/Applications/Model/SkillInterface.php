<?php

namespace Applications\Model;

use Core\Model\ModelInterface;

interface SkillInterface extends ModelInterface
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