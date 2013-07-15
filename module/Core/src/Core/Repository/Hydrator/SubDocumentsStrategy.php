<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

use Core\Entity\ModelInterface;
use Core\Entity\CollectionInterface;

class SubDocumentsStrategy implements StrategyInterface
{
    protected $entityBuilder;
    
    public function __construct($entityBuilder)
    {
        $this->entityBuilder = $entityBuilder;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return $this->entityBuilder->buildCollection($value);
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        if (!$value instanceOf \Core\Entity\CollectionInterface) {
            // @todo Error Handling
            return $value;
        }
       
        if ($value instanceOf \Core\Entity\RelationCollectionInterface
            || !count($value)
        ) {
            return null;
        }
        
        return $this->entityBuilder->unbuildCollection($value);
        
    }
    
}