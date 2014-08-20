<?php

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as Jms;
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
     * @var unknown
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     */
    protected $user;
    
    /**
     * 
     * @var unknown
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Education")
     */
    protected $educations;
    
    /**
     * 
     * @var unknown
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Employment")
     */
    protected $employments;
    
    /**
     * 
     * @var unknown
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Skill")
     */
    protected $skills;
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
	/**
     * @return the $educations
     */
    public function getEducations ()
    {
        if (!$this->educations) {
            $this->setEducations(new ArrayCollection());
        }
        return $this->educations;
    }

	/**
     * @param field_type $educations
     */
    public function setEducations (CollectionInterface $educations)
    {
        $this->educations = $educations;
        return $this;
    }

	/**
     * @return the $employments
     */
    public function getEmployments ()
    {
        if (!$this->employments) {
            $this->setEmployments(new ArrayCollection());
        }
        return $this->employments;
    }

    /**
     * @param field_type $employments
     * @return $this
     */
    public function setEmployments (CollectionInterface $employments)
    {
        $this->employments = $employments;
        return $this;
    }
    
    /**
     * @return the $skills
     */
    public function getSkills ()
    {
        if (!$this->skills) {
            $this->setSkills(new ArrayCollection());
        }
    	return $this->skills;
    }
    
    /**
     * @param field_type $employments
     */
    public function setSkills (CollectionInterface $skills)
    {
    	$this->skills = $skills;
    	return $this;
    }

    
    
}