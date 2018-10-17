<?php

namespace Cv\Entity;

use Core\Entity\EntityInterface;

interface LanguageInterface extends EntityInterface
{

    /*
     * name of the language de,en,fr
     */
    public function setLanguage($language);
    public function getLanguage();
    
    /*
     * A1 - C2
     */
    public function setLevelListening($level);
    public function getLevelListening();
    public function setLevelReading($level);
    public function getLevelReading();
    public function setLevelSpokenInteraction($level);
    public function getLevelSpokenInteraction();
    public function setLevelSpokenProduction($level);
    public function getLevelSpokenProduction();
    public function setLevelWriting($level);
    public function getLevelWriting();
}
