<?php

namespace Cv\Entity;

use Core\Entity\AbstractEntity;

class NativeLanguage extends AbstractEntity
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