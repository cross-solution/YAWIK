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

use Core\Entity\Collection\AttachedEntitiesCollection;

interface AttachableEntityInterface
{

    /**
     * @param AttachedEntitiesCollection $collection
     * @throws \LogicException If collection is already set
     */
    public function setAttachedEntitiesCollection(AttachedEntitiesCollection $collection);

    /**
     * @param IdentifiableEntityInterface $entity
     * @param string $key
     * @return AttachableEntityInterface
     */
    public function attachEntity(IdentifiableEntityInterface $entity, $key = null);

    /**
     * @param string $key
     * @return AttachableEntityInterface
     */
    public function detachEntity($key);
    
    /**
     * @param string $key
     * @return IdentifiableEntityInterface|null
     */
    public function getAttachedEntity($key);
    
    /**
     * @param string $key
     * @return bool
     */
    public function hasAttachedEntity($key);

}
