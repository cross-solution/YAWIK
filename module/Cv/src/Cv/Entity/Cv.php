<?php

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ODM\Document(collection="cvs", repositoryClass="\Cv\Repository\Cv")
 */
class Cv extends AbstractIdentifiableEntity implements CvInterface
{
    
    /**
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     */
    protected $user;
    
    /**
     *
     * @var EducationInterface
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Education")
     */
    protected $educations;
    
    /**
     *
     * @var EmploymentInterface
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Employment")
     */
    protected $employments;
    
    /**
     *
     * @var SkillInterface
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Skill")
     */
    protected $skills;
    
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
     * @return \Core\Entity\Collection\ArrayCollection
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
     * @return \Core\Entity\Collection\ArrayCollection
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
     * @return \Core\Entity\Collection\ArrayCollection
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
}
