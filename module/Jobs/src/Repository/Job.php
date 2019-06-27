<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Rafal Ksiazek <harpcio@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

namespace Jobs\Repository;

use Auth\Entity\UserInterface;
use Core\Entity\Tree\EmbeddedLeafs;
use Core\Repository\AbstractRepository;
use Core\Repository\DoctrineMongoODM;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\Query;
use Jobs\Entity\Category;
use Jobs\Entity\Classifications;
use Jobs\Entity\StatusInterface;

/**
 * Class Job
 *
 */
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
        /* @var $filter \Core\Repository\Filter\AbstractPaginationQuery  */
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
        $criteria = $this->getIsDeletedCriteria(
                ['permissions.assigned.' . $resourceId => [ '$exists' => true]]
        );

        return $this->findBy($criteria);
    }

    /**
     * Look for an drafted Document of a given user
     *
     * @param $user
     * @return \Jobs\Entity\Job|null
     */
    public function findDraft($user)
    {
        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        $criteria = $this->getIsDeletedCriteria([
            'isDraft' => true,
            'user' => $user,
        ]);

        $document = $this->findOneBy($criteria);

        if (!empty($document)) {
            return $document;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getUniqueReference()
    {
        return uniqid();
    }

    /**
     * Selects job postings of a certain organization
     *
     * @param int $organizationId
     * @return \Jobs\Entity\Job[]
     */
    public function findByOrganization($organizationId, $status = null)
    {
        $criteria = $this->getIsDeletedCriteria([
            'organization' => new \MongoId($organizationId),
        ]);

        if ($status) {
            $criteria['status.name'] = $status;
        }
        return $this->findBy($criteria);
    }

    /**
     * Selects all Organizations with Active Jobs
     *
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findActiveOrganizations($term = null)
    {
        $qb = $this->createQueryBuilder();
        $qb->distinct('organization')
            ->hydrate(true)
           ->field('status.name')->notIn([ StatusInterface::EXPIRED, StatusInterface::INACTIVE ]);

        $q = $qb->getQuery();
        $r = $q->execute();
        $r = $r->toArray();

        $qb = $this->dm->createQueryBuilder('Organizations\Entity\Organization');
        $qb->field('_id')->in($r);
        if ($term) {
            $qb->field('_organizationName')->equals(new \MongoRegex('/' . addslashes($term) . '/i'));
        }

        $q = $qb->getQuery();
        $r = $q->execute();

        return $r;
    }

    /**
     * @return  Cursor
     * @throws  \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findActiveJob($hydrate = true)
    {
        $qb = $this->createQueryBuilder()
            ->hydrate($hydrate)
            ->refresh()
            ->field('status.name')->in([StatusInterface::ACTIVE])
            ->field('isDraft')->equals(false)
        ;
        $q  = $qb->getQuery();
        $r  = $q->execute();

        return $r;
    }

    /**
     * Get jobs for given user ID
     *
     * @param string $userId
     * @param int $limit
     * @return Cursor
     * @since 0.27
     */
    public function getUserJobs($userId, $limit = null)
    {
        $qb = $this->createQueryBuilder()
            ->field('user')->equals($userId)
            ->sort(['dateCreated.date' => -1]);

        if (isset($limit)) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Create a query builder instance.
     *
     * @param bool $isDeleted Value of the isDeleted flag. Pass "null" to ignore this field.
     *
     * @return Query\Builder
     */
    public function createQueryBuilder($isDeleted = false)
    {
        $qb =  parent::createQueryBuilder();

        if (null !== $isDeleted) {
            $qb->addAnd(
               $qb->expr()->addOr($qb->expr()->field('isDeleted')->equals($isDeleted))
                          ->addOr($qb->expr()->field('isDeleted')->exists(false))
            );
        }

        return $qb;
    }

    private function getIsDeletedCriteria($criteria)
    {
        $criteria['$or'] = [
            ['isDeleted' => ['$exists' => false]],
            ['isDeleted' => false],
        ];

        return $criteria;
    }
}
