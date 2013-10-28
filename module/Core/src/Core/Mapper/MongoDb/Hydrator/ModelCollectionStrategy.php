<?php

namespace Core\Mapper\MongoDb\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Core\Model\ModelInterface;
use Core\Model\CollectionInterface;

class ModelCollectionStrategy implements StrategyInterface
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
            $this->setCollectionPrototype(new \Core\Model\Collection());
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
        $collection->addModels($value);
        return $collection;
        
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        return $value;
        if (!$value instanceOf \Core\Model\CollectionInterface) {
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