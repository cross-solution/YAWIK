<?php

namespace Core\Entity\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\DefaultStrategy;
use Core\Entity\CollectionInterface;

class ArrayToCollectionStrategy extends DefaultStrategy
{
    protected $collectionPrototype;
    
    public function __construct(CollectionInterface $collectionPrototype = null)
    {
        if (null !== $collectionPrototype) {
            $this->setCollectionPrototype($collectionPrototype);
        }
    }
    
    public function setCollectionPrototype(CollectionInterface $collection)
    {
        $this->collectionPrototype = $collection;
        return $this;
    }
    
    public function createCollection()
    {
        if (!$this->collectionPrototype) {
            $this->setCollectionPrototype(new \Core\Entity\Collection());
        }
        return clone $this->collectionPrototype;
    }
    
    public function hydrate($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        
        return $this->createCollection()->addEntities($value);
    }
    
}