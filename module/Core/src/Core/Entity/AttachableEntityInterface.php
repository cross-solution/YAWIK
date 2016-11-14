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

interface AttachableEntityInterface
{

    /**
     * @param AttachableEntityManager $attachableEntityManager
     * @throws \LogicException If attachable entity manager is already set
     */
    public function setAttachableEntityManager(AttachableEntityManager $attachableEntityManager);

    /**
     * Adds an $entity using an optional $key.
     * If $key is not provided then $entity's FQCN will be used as a key
     * Any existing $entity with the same $key will be replaced.
     *
     * @param IdentifiableEntityInterface $entity
     * @param string $key
     * @return AttachableEntityInterface
     */
    public function addAttachedEntity(IdentifiableEntityInterface $entity, $key = null);

    /**
     * @param string $key
     * @return bool
     */
    public function removeAttachedEntity($key);
    
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
