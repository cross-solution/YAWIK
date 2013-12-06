<?php

namespace Jobs\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Core\Repository\PaginatorAdapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class Job extends AbstractRepository implements EntityBuilderAwareInterface
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
	

	public function find($id, $mode = self::LOAD_LAZY)
    {
        $entity = $this->getMapper('job')->find($id);
        return $entity;
    }
    
    public function findByApplyId($applyId, $mode = self::MODE_FORCE_ENTITY)
    {
        return $this->getMapper('job')->findByApplyId((string) $applyId, $mode);
    }
    
    public function fetch()
    {
        $collection = $this->getMapper('job')->fetch();
        return $collection;
    }
    
    public function fetchRecent($userId=null)
    {

        $collection = $this->getMapper('job')->fetchRecent($userId, 5);
        return $collection;
    }
    public function fetchByUser($userOrId)
    {
        if ($userOrId instanceOf \Auth\Entity\UserInterface) {
            $userOrId = $userOrId->id;
        }
        
        $collection = $this->getMapper('job')->fetch(
            array('userId' => $userOrId)
        );
        return $collection;    
    }
    
    public function countByUser($userOrId = null)
    {
        if ($userOrId instanceOf \Auth\Entity\UserInterface) {
            $userOrId = $userOrId->id;
        }
        
        return $this->getMapper('job')->count(array('userId' => $userOrId));
    }
    
    public function getPaginatorAdapter(array $params)
    {
        $filter = $this->mappers->getServiceLocator()->get('filtermanager')->get('jobs-params-to-properties');
        $query = $filter->filter($params);
        return $this->getMapper('job')->getPaginatorAdapter($query);
        $query = array();
        foreach ($propertyFilter as $property => $value) {
            if (in_array($property, array('applyId'))) {
                $query[$property] = new \MongoRegex('/^' . $value . '/');
            }
        }
        $cursor = $this->getMapper('job')->getCursor($query); //, array('cv'), true);
        $cursor->sort($sort);
        return new PaginatorAdapter($cursor, $this->builders->get('job'));
    }
    
    public function save(EntityInterface $entity)
    {
        $this->getMapper('job')->save($entity);
    }
    
}