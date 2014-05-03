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
    protected $levelListening;
    
    /**
     * @ODM\String
     * @var unknown
     */
    protected $levelReading;

    /**
     * @ODM\String
     * @var unknown
     */
    protected $levelSpokenInteraction;

    /**
     * @ODM\String
     * @var unknown
     */
    protected $levelSpokenProduction;
    
    /**
     * @ODM\String
     * @var unknown
     */
    protected $levelWriting; 
    
    public function setLanguage($language)
    {
        $this->language = (string) $language;
        return $this;
    }
    
    public function getLanguage()
    {
        return $this->language;
    }
    
    public function setLevelListening($level)
    {
        $this->levelListening = $level;
        return $this;
    }
    
    public function getLevelListening()
    {
        return $this->levelListening;
    }

    public function setLevelReading($level)
    {
        $this->levelReading = $level;
        return $this;
    }
    
    public function getLevelReading()
    {
        return $this->levelReading;
    }
    
    public function setLevelSpokenInteraction($level)
    {
        $this->levelSpokenInteraction = $level;
        return $this;
    }
    
    public function getLevelSpokenInteraction()
    {
        return $this->levelSpokenInteraction;
    }
    
    public function setLevelSpokenProduction($level)
    {
        $this->levelReading = $level;
        return $this;
    }
    
    public function getLevelSpokenProduction()
    {
        return $this->levelSpokenProduction;
    }
    
    public function setLevelWriting($level)
    {
        $this->levelWriting = $level;
        return $this;
    }
    
    public function getLevelWriting()
    {
        return $this->levelWriting;
    }
}