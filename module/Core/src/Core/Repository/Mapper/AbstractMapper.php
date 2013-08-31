<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core MongoDb Mappers */
namespace Core\Repository\Mapper;

use Core\Entity\EntityInterface;
use Core\Repository\EntityBuilder\EntityBuilderInterface;
/**
 * Concrete implementation of \Core\Mapper\MongoDb\MapperInterface
 * 
 */
abstract class AbstractMapper implements MapperInterface
{
    
    /**
     * Mongo collection
     * 
     * @var \MongoCollection
     */
    protected $collection;
    
    public function __construct(\MongoCollection $collection)
    {
        $this->setCollection($collection);
    }
    
    /**
     * {@inheritdoc}
     * @return \Core\Mapper\MongoDb\AbstractMapper
     * @see \Core\Mapper\MongoDb\MapperInterface::setCollection()
     */
    public function setCollection(\MongoCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }
    
    /**
     * {@inheritdoc} 
     * @see \Core\Mapper\MongoDb\MapperInterface::getCollection()
     */
    public function getCollection()
    {
        return $this->collection;
    }
    
    
    /**
     * {@inheritdoc}
     * 
     * @param string|\MongoId $id Mongodb id
     */
    protected function getData($query=array(), array $fields = array(), $exclude = false)
    {
        if (!is_array($query)) {
            $query = array('_id' => $this->getMongoId($query));
        }
        $mongoFields = $this->getMongoFields($fields, $exclude);
        
        $data = $this->collection->findOne($query, $mongoFields);
        return $data;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param CriteriaInterface|null $criteria
     * @return Collection
     */
    public function getCursor(array $query = array(), array $fields = array(), $exclude = false)
    {
        $mongoQuery = isset($query['query']) ? $query['query'] : $query;
        
        $mongoFields = $this->getMongoFields($fields, $exclude);
        $cursor = $this->getCollection()->find($query, $mongoFields);
        return $cursor;
    }
    
    
    
    /**
     * Saves an application
     *
     * @param ModelInterface $model
     * @see \Core\Mapper\MapperInterface::save()
     */
    protected function saveData(array $data)
    {
        if (isset($data['id'])) {
            $query = array('_id' => $this->getMongoId($data['id']));
            unset($data['id']);
            
            $this->getCollection()->update($query, array('$set' => $data));
            return (string) $query['_id'];
            
        } else {
            $this->getCollection()->insert($data);
            return (string) $data['_id'];
        }
    }
    
    protected function getMongoId($id)
    {
        if (!$id instanceOf \MongoId) {
            $id = new \MongoId($id);
        }
        return $id;
    }
    
    protected function getMongoFields(array $fields, $exclude = false)
    {
        $mongoFields = array();
        foreach ($fields as $key => $value) {
            if (is_numeric($key)) {
                $mongoFields[$value] = !$exclude;
            } else {
                $mongoFields[$key] = $value;
            }
        }
        return $mongoFields;
    }
    
}