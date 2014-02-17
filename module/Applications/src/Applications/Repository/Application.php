<?php

namespace Applications\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Core\Paginator\Adapter\EntityList;
use Applications\Entity\ApplicationInterface;
use Doctrine\ODM\MongoDB\Events;
use Applications\Entity\Application as ApplicationEntity;

class Application extends AbstractRepository
{   
    public function getPaginatorCursor($params)
    {
        $filter = $this->getService('filterManager')->get('Applications/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        return $qb->getQuery()->execute();
    }
    
    public function getUnreadApplications($job) 
    {
        $auth=$this->getService('AuthenticationService');
        $qb=$this->createQueryBuilder()
                  ->field("readBy")->notIn($auth->getUser()->id)
                  ->field("job")->equals( new \MongoId($job->id));
        return $qb->getQuery()->execute();          
    }
    

    /**
     * @deprecated
     * @param unknown $jobId
     * @return unknown
     */
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
    
    /**
     * @deprecated
     * counts the number of applications of 
     */    
    public function countBy($userOrId, $onlyUnread=false)
    {
        if ($userOrId instanceOf \Auth\Entity\UserInterface) {
            $userOrId = $userOrId->getId();
        }
        $criteria = array('user' => $userOrId);
        if ($onlyUnread) {
            $criteria['readBy'] = array ('$ne' => $userOrId);
        }
        return $this->findBy($criteria)->count();
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
        $this->dm->remove($entity);
        $this->dm->flush();
        return $this;
    }
    
     
}