<?php

namespace Applications\Model;

use Core\Model\ModelInterface;

interface LanguageInterface extends ModelInterface
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