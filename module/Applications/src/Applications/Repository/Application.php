<?php

namespace Applications\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Core\Paginator\Adapter\EntityList;

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
              ? $this->getMapper('application')->find($id, array())
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
    
    public function getPaginatorAdapter(array $params)
    {
        return $this->getMapper('application')->getPaginatorAdapter($params);
    }
    
    public function changeStatus($application, $status)
    {
        $application->setStatus($status);
        $history = $this->builders->get('Applications/History')->build(array(
            'date' => new \DateTime(),
            'status' => $application->getStatus(),
            'message' => '[System]'
        ));
        $application->getHistory()->add($history);
        $this->save($application);
        return $this;
    }
    
    public function save(EntityInterface $entity)
    {
        $entity->setDateModified('now');
        $this->getMapper('application')->save($entity);
    }
    
    public function delete(EntityInterface $entity)
    {
        //$this->getMapper('application')->delete($entity);
        $this->getMapper('application-trash')->save($entity, true);

        return $this;
    }
    
     
}