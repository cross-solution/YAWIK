<?php

namespace Cv\Repository\Mapper;

use Core\Repository\Mapper\AbstractBuilderAwareMapper as CoreMapper;
use Core\Entity\EntityInterface;

class CvMapper extends CoreMapper
{
    
    
    
    /**
     * {@inheritdoc}
     *
     * @param string|\MongoId $id Mongodb id
     */
    public function find($id, array $fields = array(), $exclude = false)
    {
        $data = $this->getData($id, $fields, $exclude);
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
    public function fetch(array $query = array(), array $fields = array(), $exclude = false)
    {
        $cursor     = $this->getCursor($query, $fields, $exclude);
        $collection = $this->buildCollection($cursor, 'cv');
        return $collection;
    }
    
    public function fetchEducations($cvId)
    {
        $fields = array('educations' => true, '_id' => false);
        $data = $this->getData($cvId, $fields);
        $collection = $this->buildCollection($data['educations'], 'education');
        return $collection;
        
    }
    
    public function fetchEmployments($cvId)
    {
        $fields = array('employments' => true, '_id' => false);
        $data = $this->getData($cvId, $fields);
        $collection = $this->buildCollection($data['employments'], 'employment');
        return $collection;
    }
    
    public function save(EntityInterface $entity)
    {
        $builder = $this->builders->get('cv');
        $data    = $builder->unbuild($entity);
        $id      = $this->saveData($data);
        if ($id) {
            $entity->setId($id);
        }
        
    }
    
} 