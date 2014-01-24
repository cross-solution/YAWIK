<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Jobs\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Repository\PaginatorAdapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class Job extends AbstractRepository 
{
    
    protected $builders;
    
    public function getPaginatorCursor($params)
    {
        $filter = $this->getService('filterManager')->get('Jobs/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        return $qb->getQuery()->execute();
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
    
    /* was used on dashbord panel */
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
        
        return $this->findBy(array('userId' => $userOrId))->count();
    }
    
    public function save(EntityInterface $entity)
    {
        $this->getMapper('job')->save($entity);
    }
    
}