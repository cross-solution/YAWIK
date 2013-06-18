<?php

namespace Core\Repository\MongoDb\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Core\Mapper\MapperInterface;
use Core\Model\ModelInterface;
use Core\Model\CollectionInterface;

class SubDocumentsStrategy implements StrategyInterface
{
    protected $modelBuilder;
    
    public function __construct($modelBuilder)
    {
        $this->modelBuilder = $modelBuilder;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return $this->modelBuilder->buildCollection($value);
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
       
        if ($value instanceOf \Core\Model\RelationCollectionInterface
            || !count($value)
        ) {
            return null;
        }
        
        return $this->modelBuilder->unbuildCollection($value);
        
    }
    
}