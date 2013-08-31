<?php

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\CollectionInterface;

class Cv extends AbstractIdentifiableEntity implements CvInterface
{
    
    protected $educations;
    protected $employments;
    
	/**
     * @return the $educations
     */
    public function getEducations ()
    {
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
        return $this->employments;
    }

	/**
     * @param field_type $employments
     */
    public function setEmployments (CollectionInterface $employments)
    {
        $this->employments = $employments;
        return $this;
    }

    
    
}