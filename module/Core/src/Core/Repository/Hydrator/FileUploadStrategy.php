<?php

namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

use Core\Entity\ModelInterface;
use Core\Entity\CollectionInterface;

class FileUploadStrategy implements StrategyInterface
{
    
    protected $repository;
    protected $metaData;
    
    public function __construct($repository, array $metaData=array())
    {
        $this->repository = $repository;
        $this->metaData   = $metaData;
    }
    
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function hydrate ($value)
    {
        if (UPLOAD_ERR_NO_FILE == $value['error']) {
            return null;
        }
        if (count($this->metaData)) {
            $value['meta'] = $this->metaData;
        }
        $entityId = $this->repository->saveUploadedFile($value);
        $entity   = $this->repository->find((string) $entityId);
        return $entity;
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function extract ($value)
    {
        if (null === $value) {
            return null;
        }
        return (string) $value->id;
    }
    
}