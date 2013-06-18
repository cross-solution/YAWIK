<?php

namespace Applications\Model;

use Core\Model\AbstractModel;

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