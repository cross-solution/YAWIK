<?php

namespace Core\Repository;

//use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Entity\FileInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Core\Paginator\Adapter\EntityList;
use Doctrine\ODM\MongoDB\Events;

class File extends AbstractRepository
{   
    
    public function copy(FileInterface $fileDocument)
    {
        $gFile = $fileDocument->file;
        $gridFS = new \Doctrine\MongoDB\GridFSFile();
        $gridFS->setBytes($gFile->getBytes());
        $entity = $this->create();
        $entity->setFile($gridFS);
        $gridFS->getSize();
        $entity->name = $fileDocument->name;
        $entity->type = $fileDocument->type;
        
        return $entity;
    }
}