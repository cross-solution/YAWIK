<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

use Core\Entity\ModelInterface;
use Core\Entity\CollectionInterface;

class FileUploadStrategy implements StrategyInterface
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
        $entityId = $this->repository->saveUploadedFile($value);
        $entity = $this->repository->find((string) $entityId);
        return $entity;
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        return (string) $value->id;
    }
    
}