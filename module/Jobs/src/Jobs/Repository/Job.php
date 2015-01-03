<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Repository;

use Auth\Entity\UserInterface;
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
    
    public function getTypeAheadResults($query, $userId)
    {
        $qb = $this->createQueryBuilder();
        $qb->hydrate(false)
           ->select('title', 'applyId')
           ->field('permissions.view')->equals($userId)
           ->field('title')->equals(new \MongoRegex('/' . $query . '/i'))
           ->sort('title')
           ->limit(5);
        
        $result = $qb->getQuery()->execute();
        
        return $result;
        
    }

    /**
     * Look for an drafted Document of a given user
     *
     * @param $user
     * @return null
     */
    public function findDraft($user)
    {
        if ($user instanceOf UserInterface) {
            $user = $user->getId();
        }

        $document = $this->findOneBy(array(
            'isDraft' => true,
            'user' => $user
        ));

        if (!empty($document)) {
            return $document;
        }

        return null;
    }
    
}