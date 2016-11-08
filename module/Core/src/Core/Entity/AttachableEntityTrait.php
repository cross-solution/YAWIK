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
use Core\Entity\Collection\AttachedEntitiesCollection;
use LogicException;

/**
 * @see AttachableEntityInterface
 */
trait AttachableEntityTrait
{

    /**
     * @var AttachedEntitiesCollection
     * @ODM\EmbedMany(
     *      strategy="set",
     *      collectionClass="\Core\Entity\Collection\AttachedEntitiesCollection",
     *      targetDocument="\Core\Entity\AttachedEntityReference"
     * )
     */
    protected $attachedEntitiesCollection;
    
    /**
     * @see AttachableEntityInterface::setAttachedEntitiesCollection()
     */
    public function setAttachedEntitiesCollection(AttachedEntitiesCollection $collection)
    {
        if (isset($this->attachedEntitiesCollection)) {
            throw new LogicException('Attached entity collection is already set');
        }
        
        $this->attachedEntitiesCollection = $collection;
        
        return $this;
    }

    /**
     * @see AttachableEntityInterface::attachEntity()
     */
    public function attachEntity(IdentifiableEntityInterface $entity, $key = null)
    {
        $this->getAttachedEntitiesCollection()->setAttachedEntity($key, $entity);
        
        return $this;
    }

    /**
     * @see AttachableEntityInterface::detachEntity()
     */
    public function detachEntity($key)
    {
        $this->getAttachedEntitiesCollection()->removeAttachedEntity($key);
        
        return $this;
    }

    /**
     * @see AttachableEntityInterface::getAttachedEntity()
     */
    public function getAttachedEntity($key)
    {
        return $this->getAttachedEntitiesCollection()->getAttachedEntity($key);
    }
    
    /**
     * @see AttachableEntityInterface::hasAttachedEntity()
     */
    public function hasAttachedEntity($key)
    {
        return (bool) $this->getAttachedEntitiesCollection()->getAttachedEntity($key);
    }
    
    /**
     * @throws LogicException
     * @return AttachedEntitiesCollection
     */
    protected function getAttachedEntitiesCollection()
    {
        if (!isset($this->attachedEntitiesCollection)) {
            throw new LogicException('No attached entity collection is set');
        }
        
        return $this->attachedEntitiesCollection;
    }
}
