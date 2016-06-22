<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\Collection\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * initial Skill class.
 *
 * @package Cv\Entity
 * @ODM\EmbeddedDocument
 */
class Skill extends AbstractIdentifiableEntity
{
    
    /**
     * @ODM\EmbedMany(targetDocument="NativeLanguage")
     * @var ArrayCollection
     */
    protected $nativeLanguages;
    /**
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="Language")
     */
    protected $languageSkills;
    
    /**
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="ComputerSkill")
     */
    protected $computerSkills;
    
    public function __construct()
    {
        $this->nativeLanguages = new ArrayCollection();
        $this->languageSkills = new ArrayCollection();
        $this->computerSkills = new ArrayCollection();
    }

    /**
     * @param $nativeLanguages
     * @return $this
     */
    public function setNativeLanguages(Collection $nativeLanguages)
    {
        $this->nativeLanguages = $nativeLanguages;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNativeLanguages()
    {
        return $this->nativeLanguages;
    }

    /**
     * @param $languageSkills
     * @return $this
     */
    public function setLanguageSkills(Collection $languageSkills)
    {
        $this->languageSkills = $languageSkills;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLanguageSkills()
    {
        return $this->languageSkills;
    }

    /**
     * @param $computerSkills
     * @return $this
     */
    public function setComputerSkills(Collection $computerSkills)
    {
        $this->computerSkills = $computerSkills;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getComputerSkills()
    {
        return $this->computerSkills;
    }
}
