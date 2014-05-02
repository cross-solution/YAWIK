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
        //$copy = $file->copy();
        // resource is already a handle to a temp-File
        //$clone = $fileDocument->_copy();
        //$data = $fileDocument->__load();
        $gFile = $fileDocument->file;
        $gridFS = new \Doctrine\MongoDB\GridFSFile();
        $gridFS->setBytes($gFile->getBytes());
        //$resource = $fileDocument->getResource();
        //$tmp = tempnam(sys_get_temp_dir(), 'filecopy_');
        //$tmpFile = tmpfile();
        //while (!feof($resource)) {
        //    fwrite($tmpFile, fread($resource, 1024));
        //}
        //fclose($tmpFile);
        //$gFile->isDirty(True);
        //$mfile = $fileDocument->file->getMongoGridFSFile();
        //$gFile->isDirty(true);
        
        //$resource = $file->getResource();
        $entity = $this->create();
        $entity->setFile($gridFS);
        $gridFS->getSize();
        $entity->name = $fileDocument->name;
        $entity->type = $fileDocument->type;
        
        //$this->dm->persist($entity);
        //$this->dm->flush($entity);

        //unlink($tmp);
        //$this->dm->persist($entity);
        
        return $entity;
    }
}