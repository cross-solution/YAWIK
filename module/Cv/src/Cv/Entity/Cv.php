<?php

namespace Cv\Entity;

use Auth\Entity\InfoInterface;
use Auth\Entity\UserInterface;
use Core\Collection\IdentityWrapper;
use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\DraftableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @ODM\Document(collection="cvs", repositoryClass="\Cv\Repository\Cv")
 */
class Cv extends AbstractIdentifiableEntity implements CvInterface, DraftableEntityInterface
{
    
    /**
     * Owner of the CV
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     * @ODM\Index
     */
    protected $user;
    
    /**
     * personal informations, contains firstname, lastname, email,
     * phone etc.
     *
     * @ODM\EmbedOne(targetDocument="Contact")
     */
    protected $contact;
    
    /**
     * Education History
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Education")
     */
    protected $educations;
    
    /**
     * Employment History
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Employment")
     */
    protected $employments;
    
    /**
     * Skills
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Skill")
     */
    protected $skills;

    /**
    * Skills
    *
    * @var ArrayCollection
    * @ODM\EmbedMany(targetDocument="\Cv\Entity\Language")
    */
    protected $languageSkills;

    /**
     * @var array
     * @ODM\Collection
     */
    protected $nativeLanguages=[];


    /**
     * Preferred Job. Where do the user want to work? What kind of work he wants do do
     *
     * @ODM\EmbedOne(targetDocument="\Cv\Entity\PreferredJob")
     */
    protected $preferredJob;

    /**
     * Flag indicating draft state of this cv.
     *
     * @var bool
     * @ODM\Boolean
     */
    protected $isDraft = false;

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
    
    /**
     * @return Cv
     */
    public function setContact(InfoInterface $contact)
    {
        if (!$contact instanceof Contact) {
            $contact = new Contact($contact);
        }
        $this->contact = $contact;
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getEducations()
    {
        if (!$this->educations) {
            $this->setEducations(new ArrayCollection());
        }
        return $this->educations;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getEducationsIndexedById()
    {
        return new IdentityWrapper($this->getEducations());
    }

    /**
     * @param CollectionInterface $educations
     * @return $this
     */
    public function setEducations(CollectionInterface $educations)
    {
        $this->educations = $educations;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEmployments()
    {
        if (!$this->employments) {
            $this->setEmployments(new ArrayCollection());
        }
        return $this->employments;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getEmploymentsIndexedById()
    {
        return new IdentityWrapper($this->getEmployments());
    }

    /**
     * @param CollectionInterface $employments
     * @return $this
     */
    public function setEmployments(CollectionInterface $employments)
    {
        $this->employments = $employments;
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getSkills()
    {
        if (!$this->skills) {
            $this->setSkills(new ArrayCollection());
        }
        return $this->skills;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getSkillsIndexedById()
    {
        return new IdentityWrapper($this->getSkills());
    }

    /**
     * @param CollectionInterface $skills
     * @return $this
     */
    public function setSkills(CollectionInterface $skills)
    {
        $this->skills = $skills;
        return $this;
    }

    public function setIsDraft($isDraft)
    {
        $this->isDraft=$isDraft;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @return \Cv\Entity\PreferredJobInterface
     */
    public function getPreferredJob()
    {
        if (null == $this->preferredJob) {
            $this->preferredJob = new PreferredJob();
        }
        return $this->preferredJob;
    }

    /**
     * @param \Cv\Entity\PreferredJobInterface $preferredJob
     * @return $this
     */
    public function setPreferredJob(\Cv\Entity\PreferredJobInterface $preferredJob)
    {
        $this->preferredJob = $preferredJob;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLanguageSkills()
    {
        if (!$this->languageSkills) {
            $this->setLanguageSkills(new ArrayCollection());
        }
        return $this->languageSkills;
    }

    /**
     * @param CollectionInterface $skills
     * @return $this
     */
    public function setLanguageSkills(CollectionInterface $languageSkills)
    {
        $this->getLanguageSkills = $languageSkills;
        return $this;
    }
    /**
     * @return ArrayCollection
     */
    public function getLanguageSkillsIndexedById()
    {
        return new IdentityWrapper($this->getLanguageSkills());
    }
    /**
     * Sets the mothers tongue of the candidate
     *
     * @param array
     * @return $this
     */
    public function setNativeLanguages($nativeLanguages)
    {
        $this->nativeLanguages=$nativeLanguages;
        return $this;
    }

    /**
     * Gets the mothers tongue of the candidate
     *
     * @return string
     */
    public function getNativeLanguages()
    {
        return $this->nativeLanguages;
    }
}
