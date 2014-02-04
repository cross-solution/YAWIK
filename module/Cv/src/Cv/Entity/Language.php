<?php

namespace Cv\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Language extends AbstractEntity implements LanguageInterface
{
    
    /**
     * 
     * @var unknown
     * @ODM\String
     */
    protected $language;
    
    /**
     * @ODM\String
     * @var unknown
     */
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