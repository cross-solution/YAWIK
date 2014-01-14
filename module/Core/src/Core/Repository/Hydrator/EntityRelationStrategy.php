<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Core\Entity\RelationEntity;
use Core\Entity\IdentifiableEntityInterface;

class EntityRelationStrategy implements StrategyInterface
{
    
    
    protected $repository;
    
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        $entity = new RelationEntity(
            array($this->repository, 'find'),
            array($value)
        );
        return $entity;
    }
    

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        try {
            return (string) $value->id;
        } catch (\Exception $e) {
            return null;
        }
    }
    
}