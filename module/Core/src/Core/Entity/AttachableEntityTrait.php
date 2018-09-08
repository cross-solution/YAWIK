<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use LogicException;

/**
 * @see AttachableEntityInterface
 */
trait AttachableEntityTrait
{

    /**
     * @var AttachableEntityManager
     */
    protected $attachableEntityManager;

    /**
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $attachableEntityReferences = [];
    
    /**
     * @see AttachableEntityInterface::setAttachableEntityManager()
     */
    public function setAttachableEntityManager(AttachableEntityManager $attachableEntityManager)
    {
        $this->attachableEntityManager = $attachableEntityManager->setReferences($this->attachableEntityReferences);
        
        return $this;
    }

    /**
     * @see AttachableEntityInterface::addAttachedEntity()
     */
    public function addAttachedEntity(IdentifiableEntityInterface $entity, $key = null)
    {
        $this->getAttachableEntityManager()->addAttachedEntity($entity, $key);
        
        return $this;
    }

    /**
     * @see AttachableEntityInterface::removeAttachedEntity()
     */
    public function removeAttachedEntity($key)
    {
        return $this->getAttachableEntityManager()->removeAttachedEntity($key);
    }

    /**
     * @see AttachableEntityInterface::getAttachedEntity()
     */
    public function getAttachedEntity($key = null)
    {
        if (!isset($key)) {
            // allow ommiting parameter for Core\Entity\Hydrator\EntityHydrator::extract()
            return;
        }
        
        return $this->getAttachableEntityManager()->getAttachedEntity($key);
    }

    /**
     * @see AttachableEntityInterface::createAttachedEntity()
     */
    public function createAttachedEntity($entityClass, $values = [], $key=null)
    {
        return $this->getAttachableEntityManager()->createAttachedEntity($entityClass, $values, $key);
    }
    
    /**
     * @see AttachableEntityInterface::hasAttachedEntity()
     */
    public function hasAttachedEntity($key)
    {
        return (bool) $this->getAttachableEntityManager()->getAttachedEntity($key);
    }
    
    /**
     * @throws LogicException
     * @return AttachableEntityManager
     */
    protected function getAttachableEntityManager()
    {
        if (!isset($this->attachableEntityManager)) {
            throw new LogicException('No attachable entity manager is set');
        }
        
        return $this->attachableEntityManager;
    }
}
