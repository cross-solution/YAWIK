<?php

namespace Applications\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Core\Paginator\Adapter\EntityList;
use Applications\Entity\ApplicationInterface;

class Application extends AbstractRepository
{
    protected $builders;
    
    public function getPaginatorCursor($params)
    {
        $filter = $this->getService('filterManager')->get('Applications/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        return $qb->getQuery()->execute();
    }
    
    public function getUnreadApplications($job) {
        $auth=$this->getService('AuthenticationService');
        return $this->findBy(array("readBy"=>$auth->getUser()->id));    
#       return $this->findBy(array("readBy"=>$auth->getIdentity()));
    }
    
    public function setEntityBuilderManager(ServiceLocatorInterface $entityBuilderManager)
    {
        $this->builders = $entityBuilderManager;
        return $this;
    }
   
    public function getEntityBuilderManager()
    {
        return $this->builders;
    }
    
#	public function find ($id, $mode=self::LOAD_LAZY)
#    {
#        $entity = $mode == self::LOAD_EAGER
#              ? $this->getMapper('application')->find($id, array())
#              : $this->getMapper('application')->find($id, 
#                      array('cv'),
#                      /*exclude*/ true
#              );
#        
#        
#        return $entity;
#    }
    
   
    
 #   public function fetch ($mode=self::LOAD_LAZY)
 #   {
 #       $fields = array('cv' => false);
 #       
 #       $collection = $this->getMapper('application')->fetch(array(), $fields);
 #       return $collection;
 #   }
    
    public function fetchByJobId($jobId)
    {
        $collection = $this->getMapper('application')->fetch(
            array('jobId' => $jobId),
            array('cv'),
            true
        );
        return $collection;
    }
    
    public function fetchRecent($limit=5)
    {
        $collection = $this->getMapper('application')->fetchRecent($limit);
        return $collection;
    }
    
    /*
     * counts the number of applications of 
     */    
    public function countBy($userOrId, $onlyUnread=false)
    {
        $auth=$this->getService('AuthenticationService');
        return $this->findBy(array("readBy"=>$auth->getUser()->id));
        #
        if ($userOrId instanceOf \Auth\Entity\UserInterface) {
            $userOrId = $userOrId->getId();
        }
#        return $this->findBy(array->countBy($userOrId, $onlyUnread);
      #  return $this->findBy(array("readBy"=>$auth->getUser()->id));
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
    
    public function save(ApplicationInterface $application, $resetModifiedDate=true)
    {
        if ($resetModifiedDate) {
            $application->setDateModified('now');
        }
        $this->dm->persist($application);
        $this->dm->flush();
    }
    
    public function delete(EntityInterface $entity)
    {
        $this->getMapper('application')->delete($entity);
        $this->getMapper('application-trash')->save($entity, true);

        return $this;
    }
    
     
}