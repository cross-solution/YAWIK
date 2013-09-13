<?php

namespace Applications\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Application extends AbstractRepository implements EntityBuilderAwareInterface
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
    
	public function find ($id, $mode=self::LOAD_LAZY)
    {
        $entity = $mode == self::LOAD_EAGER
              ? $this->getMapper('application')->find($id)
              : $this->getMapper('application')->find($id, 
                      array('cv'),
                      /*exclude*/ true
              );
        
        
        return $entity;
    }
    
    public function fetch ($mode=self::LOAD_LAZY)
    {
        $fields = array('cv' => false);
        
        $collection = $this->getMapper('application')->fetch(array(), $fields);
        return $collection;
    }
    
    public function fetchByJobId($jobId)
    {
        $collection = $this->getMapper('application')->fetch(
            array('jobId' => $jobId),
            array('cv'),
            true
        );
        return $collection;
    }
    
    public function getPaginatorAdapter(array $propertyFilter, $sort)
    {
        
        $query = $this->builders->getServiceLocator()->get('FilterManager')
                           ->get('applications-params-to-properties')
                           ->filter($propertyFilter);
         
        
        $cursor = $this->getMapper('application')->getCursor($query, array('cv'), true); //, array('cv'), true);
        $cursor->sort($sort);
        return new ApplicationPaginatorAdapter($cursor, $this->builders->get('application'));
    }
    
    
    public function save(EntityInterface $entity)
    {
        $entity->setDateModified('now');
        $this->getMapper('application')->save($entity);
    }
    
     
}