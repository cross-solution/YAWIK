<?php

namespace Core\Mapper\MongoDb\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class ModelCollectionStrategy implements StrategyInterface
{
    
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        return $value;
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        if (!$value instanceOf \Core\Model\CollectionInterface) {
            // @todo Error Handling
            return "";
        }
        
        
        
    }
    
}