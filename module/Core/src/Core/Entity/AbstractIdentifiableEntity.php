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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * 
 * @ODM\MappedSuperclass
  */
abstract class AbstractIdentifiableEntity extends AbstractEntity implements IdentifiableEntityInterface
{
       
    /**
     * Entity id
     * 
     * @var mixed
     * @ODM\Id
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