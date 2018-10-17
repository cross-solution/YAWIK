<?php

namespace Core\Repository;

use Core\Entity\EntityInterface;
use \Doctrine\ODM\MongoDB as ODM;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractProviderRepository extends AbstractRepository
{
    public function findOneBy(array $criteria)
    {
        $document = parent::findOneBy($criteria);
        if (isset($document)) {
            $document->injectRepository($this);
        }
        return $document;
    }
    
    public function create(array $data = null)
    {
        $document = parent::create($data);
        $document->injectRepository($this);
        return $document;
    }
}
