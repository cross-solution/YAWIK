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

use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;


/**
 * Concrete implementation of \Core\Mapper\MongoDb\MapperInterface
 * 
 */
abstract class AbstractMapper implements MapperInterface, EntityBuilderAwareInterface
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
    public function find($id, array $fields = array(), $exclude = false)
    {
        $id = $this->getMongoId($id);
        $mongoFields = $this->getMongoFields($fields, $exclude);
        
        $data = $this->collection->findOne(array('_id' => $id), $mongoFields);
        return $data;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param CriteriaInterface|null $criteria
     * @return Collection
     */
    public function fetchAll(array $query = array(), array $fields = array(), $exclude = false)
    {
        $mongoQuery = isset($query['query']) ? $query['query'] : $query;
        
        $mongoFields = $this->getMongoFields($fields, $exclude);
        $cursor = $this->getCollection()->find($query);
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