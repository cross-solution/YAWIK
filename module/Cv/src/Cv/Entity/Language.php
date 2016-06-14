<?php

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Language extends AbstractIdentifiableEntity implements LanguageInterface
{
    
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $language;
    
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $levelListening;
    
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $levelReading;

    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $levelSpokenInteraction;

    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $levelSpokenProduction;
    
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $levelWriting;

    /**
     * @param $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = (string) $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevelListening($level)
    {
        $this->levelListening = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelListening()
    {
        return $this->levelListening;
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevelReading($level)
    {
        $this->levelReading = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelReading()
    {
        return $this->levelReading;
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevelSpokenInteraction($level)
    {
        $this->levelSpokenInteraction = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSpokenInteraction()
    {
        return $this->levelSpokenInteraction;
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevelSpokenProduction($level)
    {
        $this->levelSpokenProduction = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelSpokenProduction()
    {
        return $this->levelSpokenProduction;
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevelWriting($level)
    {
        $this->levelWriting = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelWriting()
    {
        return $this->levelWriting;
    }
}
