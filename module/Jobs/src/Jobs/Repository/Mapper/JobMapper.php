<?php

namespace Jobs\Repository\Mapper;

use Core\Repository\Mapper\AbstractBuilderAwareMapper as CoreMapper;
use Core\Entity\EntityInterface;

class JobMapper extends CoreMapper
{
    
    
    
    /**
     * {@inheritdoc}
     *
     * @param string|\MongoId $id Mongodb id
     */
    public function find($id, array $fields = array(), $exclude = false)
    {
        $data = $this->getData($id, $fields, $exclude);
        if (null === $data) { return $this->builders->get('job')->getEntity(); }
        $builder = $this->builders->get('job');
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
        $collection = $this->buildCollection($cursor, 'job');
        return $collection;
    }
    
    
    
    public function save(EntityInterface $entity)
    {
        $builder = $this->builders->get('job');
        $data    = $builder->unbuild($entity);
        $id      = $this->saveData($data);
        if ($id) {
            $entity->setId($id);
        }
        
    }
    
} 