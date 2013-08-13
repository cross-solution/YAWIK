<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core Entitys */
namespace Core\Entity;

/**
 * Concrete implementation of \Core\Entity\EntityInterface.
 * 
 * Provides some magic function for accessing properties
 * as class members, mirroring these calls to the
 * getter and setter methods.
 * 
 */
abstract class AbstractIdentifiableEntity extends AbstractEntity implements IdentifiableEntityInterface
{
       
    /**
     * Entity id
     * 
     * @var mixed
     */
    protected $id;
    
    /**
     * {@inheritdoc}
     * @see \Core\Entity\EntityInterface::getId()
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * {@inheritdoc}
     * @return \Core\Entity\AbstractEntity
     * @see \Core\Entity\EntityInterface::setId()
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    
}