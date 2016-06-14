<?php

namespace Cv\Entity;

use Doctrine\Common\Collections\Collection as CollectionInterface;
use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;

interface CvInterface extends EntityInterface, IdentifiableEntityInterface
{
    
    /**
     * @return CollectionInterface $educations
     */
    public function getEducations();
    
    /**
     * @param CollectionInterface $educations
     */
    public function setEducations(CollectionInterface $educations);
    
    /**
     * @return CollectionInterface $employments
     */
    public function getEmployments();
    
    /**
     * @param CollectionInterface $employments
     */
    public function setEmployments(CollectionInterface $employments);
}
