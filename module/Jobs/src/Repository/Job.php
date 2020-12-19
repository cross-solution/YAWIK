<?php
/**
 * YAWIK
 *
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Rafal Ksiazek <harpcio@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

namespace Jobs\Repository;

use Auth\Entity\UserInterface;
use Core\Repository\AbstractRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Jobs\Entity\Job as JobEntity;
use Jobs\Entity\StatusInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use Organizations\Entity\Organization;

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
     * @throws MongoDBException
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
     * @return JobEntity|null
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
     * @return JobEntity[]
     */
    public function findByOrganization($organizationId, $status = null)
    {
        $criteria = $this->getIsDeletedCriteria([
            'organization' => new ObjectId($organizationId),
        ]);

        if ($status) {
            $criteria['status.name'] = $status;
        }
        return $this->findBy($criteria);
    }

    /**
     * Selects all Organizations with Active Jobs
     *
     * @param ?string $term
     * @param bool $execute
     * @return null|Organization[]|QueryBuilder
     * @throws MongoDBException
     */
    public function findActiveOrganizations(?string $term = null, bool $execute = true)
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
            $qb->field('_organizationName')
                ->equals(new Regex('/' . addslashes($term) . '/i'));
        }

        if($execute){
            return $qb->getQuery()->execute();
        }

        return $qb;
    }

    /**
     * @param bool $hydrate
     * @return array|JobEntity[]
     * @throws MongoDBException
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
     * @param int|null $limit
     * @return JobEntity[]|null
     * @throws MongoDBException
     * @since 0.27
     */
    public function getUserJobs(string $userId, ?int $limit = null)
    {
        $qb = $this->userJobsQuery($userId);

        if (!is_null($limit)) {
            $qb->limit($limit);
        }
        return $qb->getQuery()->execute();
    }

    /**
     * @param string $userId
     * @return int
     * @throws MongoDBException
     */
    public function countUserJobs(string $userId): int
    {
        return $this->userJobsQuery($userId)
            ->count()
            ->getQuery()
            ->execute();
    }

    private function userJobsQuery(string $userId)
    {
        return $this->createQueryBuilder()
            ->field('user')
            ->equals($userId)
            ->sort(['dateCreated.date' => -1]);
    }

    /**
     * Create a query builder instance.
     *
     * @param bool $isDeleted Value of the isDeleted flag. Pass "null" to ignore this field.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($isDeleted = false): QueryBuilder
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

    public function findOneByApplyId(string $appId)
    {
        return $this->findOneBy(['applyId' => $appId]);
    }
}
