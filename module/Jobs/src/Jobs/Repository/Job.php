<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Jobs\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Repository\PaginatorAdapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class Job extends AbstractRepository 
{
    
    public function getPaginatorCursor($params)
    {
        $filter = $this->getService('filterManager')->get('Jobs/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        return $qb->getQuery()->execute();
    }
    
    public function existsApplyId($applyId)
    {
        $qb = $this->createQueryBuilder();
        $qb->hydrate(false)
           ->select('applyId')
           ->field('applyId')->equals($applyId);
           
       $result = $qb->getQuery()->execute();
       $count = $result->count();
       return (bool) $count;
        
    }
    
    public function findByAssignedPermissionsResourceId($resourceId)
    {
        return $this->findBy(array(
            'permissions.assigned.' . $resourceId => array(
                '$exists' => true
            )
        ));
    }
    
    /**
     * @deprecated
     * @param string $userId
     * @return unknown
     */
    public function fetchRecent($userId=null)
    {
        $collection = $this->getMapper('job')->fetchRecent($userId, 5);
        return $collection;
    }
    /**
     * @deprecated
     * @param unknown $userOrId
     * @return unknown
     */
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
    
    /**
     * count jobs by user entity or user id
     * @deprecated
     * @param mixed $userOrId
     */
    public function countByUser($userOrId = null)
    {
        if ($userOrId instanceOf \Auth\Entity\UserInterface) {
            $userOrId = $userOrId->id;
        }
        
        return $this->findBy(array('userId' => $userOrId))->count();
    }
    
    public function getTypeAheadResults($query, $userId)
    {
        $qb = $this->createQueryBuilder();
        $qb->hydrate(false)
           ->select('title', 'applyId')
           ->field('permissions.view')->equals($userId)
           ->field('title')->equals(new \MongoRegex('/' . $query . '/i'))
           ->sort('title');
        
        $result = $qb->getQuery()->execute();
        
        return $result;
        
    }
    
}