<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;

interface NativeLanguageInterface extends EntityInterface
{
	/*
	 * name of the language de,en,fr
	 */
    public function setLanguage($language);
    public function getLanguage();
    
}