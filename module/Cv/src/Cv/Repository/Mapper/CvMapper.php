<?php

namespace Cv\Repository\Mapper;

use Core\Repository\Mapper\AbstractMapper;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\EntityInterface;

class CvMapper extends AbstractMapper implements EntityBuilderAwareInterface
{
    
    protected $builders;
     
    
    public function setEntityBuilderManager(ServiceLocatorInterface $entityBuilderManager)
    {
        $this->builders = $entityBuilderManager;
        return $this;
    }
    
    public function getEntityBuilderManager()
    {
        return $this->builders;
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
        $builder = $this->builders->get('cv');
        $entity = $builder->build($data);
        return $entity;
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
        $cursor = $this->getCollection()->find($query, $mongoFields);
        $builder = $this->builders->get('cv');
        $collection = $builder->buildCollection($cursor);
        return $collection;
    }
    
    public function fetchEducations($cvId)
    {
        $query = array('_id' => $this->getMongoId($cvId));
        $fields = array('educations' => true, '_id' => false);
        $data = $this->getCollection()->findOne($query, $fields);
        
        $collection = $this->builders->get('education')->buildCollection($data['educations']);
        return $collection;
        
    }
    
    public function fetchEmployments($cvId)
    {
        $query = array('_id' => $this->getMongoId($cvId));
        $fields = array('employments' => true, '_id' => false);
        $data = $this->getCollection()->findOne($query, $fields);
        
        $collection = $this->builders->get('employments')->buildCollection($data['educations']);
        return $collection;
    }
    
    public function save(EntityInterface $entity)
    {
        $data = $this->builders->get('cv')->unbuild($entity);
        return parent::saveData($data);
    }
    
} 