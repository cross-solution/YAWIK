<?php

namespace Applications\Entity;

use Core\Entity\AbstractEntity;

class NativeLanguage extends AbstractModel
{
    
    protected $language;
    
    public function setLanguage($language)
    {
        $this->language = (string) $language;
        return $this;
    }
    
    public function getLanguage()
    {
        return $this->language;
    }
    
}