<?php

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Core\Entity\DraftableEntityInterface;
use Auth\Entity\InfoInterface;

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
}
