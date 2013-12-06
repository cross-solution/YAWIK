<?php

namespace Jobs\Repository\Mapper;

use Core\Repository\Mapper\AbstractBuilderAwareMapper as CoreMapper;
use Core\Entity\EntityInterface;
use Core\Repository\AbstractRepository;
use Core\Paginator\Adapter\MongoCursor as MongoCursorAdapter;

class JobMapper extends CoreMapper
{
    
    public function getPaginatorAdapter(array $query)
    {
    
         
        if (isset($query['sort'])) {
            $sort = $query['sort'];
            unset($query['sort']);
        } else {
            $sort = array();
        }
        $cursor = $this->getCursor($query);
        $cursor->sort($sort);
        return new MongoCursorAdapter($cursor, $this->builders->get('job'));
    }
    
    
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
    
    public function findByApplyId($applyId, $mode = AbstractRepository::MODE_FORCE_ENTITY)
    {
        //$cursor     = $this->getCursor(array('applyId' => $applyId), $fields, $exclude);
        //$collection = $this->buildCollection($cursor, 'job');
        //return $collection;
        
        $data = $this->getData(array('applyId' => $applyId));
        $builder = $this->builders->get('job');
        if (empty($data)) {
            $entity = $builder->getEntity();
        }
        else {
            $entity = $builder->build($data);
        }
        
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
    
    public function fetchRecent($userId=null, $limit = 5)
    {
        $query = $userId ? array('userId' => $userId) : array();
        $cursor     = $this->getCursor($query);
        $cursor->sort(array('datePublishStart' => -1))->limit($limit);
        $collection = $this->buildCollection($cursor, 'job');
        return $collection;
    }
    public function count(array $query = array())
    {
        $cursor = $this->getCursor($query);
        return $cursor->count();
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