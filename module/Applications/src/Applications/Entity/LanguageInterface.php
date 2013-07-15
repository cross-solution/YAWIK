<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;

interface LanguageInterface extends EntityInterface
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
    
    
    
}