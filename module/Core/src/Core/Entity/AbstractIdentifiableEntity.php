<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
     * @see \Core\Entity\EntityInterface::setId()
     * @return  $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
