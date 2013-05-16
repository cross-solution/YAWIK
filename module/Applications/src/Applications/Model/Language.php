<?php

namespace Applications\Model;

use Core\Model\AbstractModel;

class Language extends AbstractModel implements LanguageInterface
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