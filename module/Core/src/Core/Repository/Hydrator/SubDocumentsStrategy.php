<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

use Core\Entity\ModelInterface;
use Core\Entity\CollectionInterface;

class SubDocumentsStrategy implements StrategyInterface
{
    
    const AS_COLLECTION = 'AS_COLLECTION';
    const AS_ENTITY     = 'AS_ENTITY';
    
    protected $entityBuilder;
    protected $buildMode;
    
    public function __construct($entityBuilder, $buildMode=self::AS_COLLECTION)
    {
        $this->entityBuilder = $entityBuilder;
        $this->buildMode = $buildMode;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return self::AS_COLLECTION == $this->buildMode 
            ? $this->entityBuilder->buildCollection($value)
            : $this->entityBuilder->build($value);
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
       
        if (($value instanceOf \Core\Entity\RelationCollectionInterface && !$value->isCollectionLoaded())
            || !count($value)
        ) {
            return null;
        }
        
        return self::AS_COLLECTION == $this->buildMode 
            ? $this->entityBuilder->unbuildCollection($value)
            : $this->entityBuilder->unbuild($value);
        
    }
    
}