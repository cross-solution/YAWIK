<?php

namespace Applications\Entity;

use Core\Entity\AbstractEntity;

class Language extends AbstractEntity implements LanguageInterface
{
    
    protected $language;
    protected $level;
    
    public function setLanguage($language)
    {
        $this->language = (string) $language;
        return $this;
    }
    
    public function getLanguage()
    {
        return $this->language;
    }
    
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
}