<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
    /**
     * Gets a pagination cursor to the jobs collection
     *
     * @param $params
     * @return mixed
     */
    public function getPaginatorCursor($params)
    {
        $filter = $this->getService('filterManager')->get('Jobs/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        return $qb->getQuery()->execute();
    }

    /**
     * Checks, if a job posting with a certain applyId (external job id) exists
     *
     * @param $applyId
     * @return bool
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
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

    /**
     * @param $resourceId
     * @return array
     */
    public function findByAssignedPermissionsResourceId($resourceId)
    {
        return $this->findBy(array(
            'permissions.assigned.' . $resourceId => array(
                '$exists' => true
            )
        ));
    }

    /**
     * Gets the Job Titles of a certain user.
     *
     * @param $query
     * @param $userId
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
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
     * @return \Jobs\Entity\Job|null
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