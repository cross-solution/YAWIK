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
use Core\Repository\EntityBuilder\EntityBuilderInterface;
use Core\Repository\Hydrator\EntityHydrator;

class FileMapper extends AbstractMapper
{
    protected $builder;
    
    public function __construct(\MongoCollection $collection, EntityBuilderInterface $fileBuilder)
    {
        parent::__construct($collection);
        $this->setFileBuilder($fileBuilder);
    }
    
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
        $cursor = $this->getCursor($query, $fields);
        $count = $cursor->count();
        
        $collection = $this->builder->buildCollection($cursor);
        return $collection;
    }
    
    public function fetchByIds(array $ids)
    {
        for ($i=0,$c=count($ids); $i<$c; $i+=1) {
            $ids[$i] = $this->getMongoId($ids[$i]);
        }
        return $this->fetch(array('_id' => array('$in' => $ids)));
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
        if (isset($fileData['meta'])) {
            $meta = array_merge($meta, $fileData['meta']);
        }
        
        $fileId = $this->collection->storeFile($fileData['tmp_name'], $meta); 
        
        return $fileId;
    }
    
    public function saveCopy(EntityInterface $file)
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'fileCopy-');
        $target = fopen($tmpName, 'w');
        $origin = $file->getResource();
        
        while (!feof($origin)) {
            fwrite($target, fread($origin, 1024));
        }
        
        fclose($origin);
        $now      = new \DateTime('now');
        $hydrator = new EntityHydrator();
        $meta = $hydrator->extract($file);
        unset($meta['type'], $meta['name'], $meta['dateUploaded']);
        $meta['dateUploaded'] = array(
            'date' => new \MongoDate($now->getTimestamp()),
            'tz' => $now->getTimezone()->getName(),
        );
        $meta['mimetype'] = $file->type;
        $meta['filename'] = $file->name;
        $meta['allowedUserIds'] = $file->allowedUserIds;
        
        $fileId = $this->collection->storeFile($tmpName, $meta);
        fclose($target);
        unlink($tmpName);
        return $fileId; 
        
    }
    
    public function delete($id)
    {
        $query = array('_id' => $this->getMongoId($id));
        return $this->collection->remove($query);
    }
}

