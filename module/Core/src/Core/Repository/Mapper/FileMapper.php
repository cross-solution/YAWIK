<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileMapper.php */ 
namespace Core\Repository\Mapper;

use \Core\Entity\EntityInterface;

class FileMapper extends AbstractMapper
{
    protected $builder;
    
    public function setCollection(\MongoCollection $collection)
    {
        if (!$collection instanceOf \MongoGridFS) {
            throw new \InvalidArgumentException('Instance of MongoGridFS expected, but received "' . get_class($collection) . '"');
        }
        
        return parent::setCollection($collection);
    }
    
    public function setFileBuilder($builder)
    {
        $this->builder = $builder;
    }

    public function find($id, array $fields=array(), $exclude = false)
    {
        $file = $this->collection->findOne(array('_id' => $this->getMongoId($id)));
        if (!$file) { return null; }
        $entity = $this->builder->build($file);
        return $entity;
    }
    
    public function fetch(array $query = array(), array $fields = array())
    {
        
    }
    
    
    public function save(EntityInterface $entity)
    {
        if ($entity->id) {
            $this->collection->remove(array('_id' => $this->getMongoId($entity->id)));
        }
        $builder = $this->builder;
        $data    = $builder->unbuild($entity);
        $id      = $this->saveData($data);
        if ($id) {
            $entity->setId($id);
        }
    }
    
    public function saveUploadedFile(array $fileData)
    {
        $fileName = $fileData['name'];
        $now      = new \DateTime('now');
        $meta = array(
            'dateUploaded' => array(
                'date' => new \MongoDate($now->getTimestamp()),
                'tz' => $now->getTimezone()->getName(),
            ),
            'mimetype' => $fileData['type'],
            'filename' => $fileName,
        );
        
        $fileId = $this->collection->storeBytes(file_get_contents($fileData['tmp_name']), $meta); 
            
        
        return $fileId;
        
        
    }
}

