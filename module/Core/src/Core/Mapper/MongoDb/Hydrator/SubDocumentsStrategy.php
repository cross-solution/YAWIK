<?php

namespace Core\Mapper\MongoDb\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Core\Mapper\MapperInterface;
use Core\Model\ModelInterface;
use Core\Model\CollectionInterface;

class SubDocumentsStrategy implements StrategyInterface
{
    protected $mapper;
    
    public function __construct(MapperInterface $mapper)
    {
        $this->setMapper($mapper);
         
    }
    
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }
    
    public function getMapper()
    {
        return $this->mapper;
    }
    
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        
        $collection = $this->getMapper()->createCollection($value);
        
        return $collection;
        
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        if (!$value instanceOf \Core\Model\CollectionInterface) {
            // @todo Error Handling
            return $value;
        }
        
        $hydrator = $this->getMapper()->getModelHydrator();
        
        $result = array();
        foreach ($value as $model) {
            $result[] = $hydrator->extract($model);
        }
        return $result;
    }
    
}