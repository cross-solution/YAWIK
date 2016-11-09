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

use Core\Repository\RepositoryService;

class AttachableEntityManager
{

    /**
     * @var RepositoryService
     */
    protected $repositories;

    /**
     * @var array
     */
    protected $references = [];

    /**
     * @param RepositoryService $repositories
     */
    public function __construct(RepositoryService $repositories)
    {
        $this->repositories = $repositories;
    }
    
    /**
     * @param array $references
     * @return AttachableEntityManager
     */
    public function setReferences(array & $references)
    {
        $this->references = & $references;
        
        return $this;
    }

    /**
     * @param IdentifiableEntityInterface $entity
     * @param string $key
     * @return AttachableEntityManager
     */
    public function setAttachedEntity(IdentifiableEntityInterface $entity, $key = null)
    {
        $className = get_class($entity);
        
        if (! isset($key)) {
            $key = $className;
        }
        
        $reference = [
            'repository' => $className
        ];
        $entityId = $entity->getId();
        
        // check if entity is not persisted
        if (! $entityId) {
            // persist entity & retrieve its ID
            $this->repositories->getRepository($className)->store($entity);
            $entityId = $entity->getId();
        }
        
        $reference['id'] = $entityId;
        $this->references[$key] = $reference;
        
        return $this;
    }

    /**
     * @param string $key
     * @return IdentifiableEntityInterface|null
     */
    public function getAttachedEntity($key)
    {
        if (! isset($this->references[$key])) {
            return;
        }
        
        $reference = $this->references[$key];
        $entity = $this->repositories->getRepository($reference['repository'])
            ->find($reference['id']);
        
        if (! $entity) {
            // remove reference if entity does not exists
            unset($this->references[$key]);
        }
        
        return $entity;
    }

    /**
     * @param string $key
     * @return bool Whether entity with given $key existed
     */
    public function removeAttachedEntity($key)
    {
        if (isset($this->references[$key])) {
            unset($this->references[$key]);
            return true;
        }
        
        return false;
    }
}
