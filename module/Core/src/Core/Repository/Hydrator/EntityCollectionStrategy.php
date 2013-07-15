<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Core\Entity\EntityInterface;
use Core\Entity\CollectionInterface;

class EntityCollectionStrategy implements StrategyInterface
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
        
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        
        $collection = $this->createCollection();
        $collection->addEntities($value);
        return $collection;
        
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        return $value;
        if (!$value instanceOf \Core\Entity\CollectionInterface) {
            // @todo Error Handling
            return $value;
        }
        
        $hydrator = $this->getHydrator();
        
        $result = array();
        foreach ($value as $model) {
            $result[] = $hydrator->extract($model);
        }
        return $result;
    }
    
}