<?php

namespace Applications\Model;

use Core\Model\ModelInterface;

interface NativeLanguageInterface extends ModelInterface
{
	/*
	 * name of the language de,en,fr
	 */
    public function setLanguage($language);
    public function getLanguage();
    
}