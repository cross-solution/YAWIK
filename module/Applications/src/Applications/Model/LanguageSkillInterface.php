<?php

namespace Applications\Model;

use Core\Model\ModelInterface;

interface LanguageSkillInterface extends ModelInterface
{
	/*
	 * name of the language de,en,fr
	 */
    public function setLanguage($language);
    public function getLanguage();
    
    /*
     * A1 - C2
     */
    public function setLevel($level);
    public function getLevel();
    
    /*
     * listening, speeking, writing
     */
    public function setType($level);
    public function getType();
    
    
}