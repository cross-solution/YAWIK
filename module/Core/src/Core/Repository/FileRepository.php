<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AbstractFileRepository.php */ 
namespace Core\Repository;

use Core\Entity\EntityInterface;
use Core\Entity\FileEntityInterface;

class FileRepository implements RepositoryInterface
{
    protected $mapper;
    
    public function __construct($mapper)
    {
        $this->mapper = $mapper;
    }
    
    public function getMapper()
    {
        return $this->mapper;
    }
    
    public function create($data = null)
    {
        $file = $this->mapper->create($data);
        return $file;
    }
    
    public function find($id) 
    {
        $file = $this->mapper->find($id);
        return $file;
    }
    
    public function fetch() {}
    public function save(EntityInterface $file) 
    {
        $this->mapper->save($file);    
    }
    
    public function saveUploadedFile(array $fileData)
    {
        $file = $this->mapper->saveUploadedFile($fileData);
        return $file;
    }
    
    public function saveCopy(EntityInterface $file)
    {
        $newFileId = $this->mapper->saveCopy($file);
        $newFile   = $this->find($newFileId);
        return $newFile;
    }
    
    public function delete($fileOrId)
    {
        if ($fileOrId instanceOf EntityInterface) {
            $fileOrId = $fileOrId->getId();
        }
        return $this->mapper->delete($fileOrId);
    }
}

