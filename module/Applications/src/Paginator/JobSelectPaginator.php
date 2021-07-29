<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Paginator;

use Core\Paginator\Adapter\DoctrineMongoAdapter;
use Laminas\Paginator\Paginator;
use Jobs\Repository\Job as JobRepository;
use MongoDB\BSON\Regex;

/**
 * Paginator for Job title select element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelectPaginator extends Paginator
{
    /**
     * @var JobRepository
     */
    private JobRepository $repository;

    public function __construct(JobRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param JobRepository $repository
     */
    public function setRepository(JobRepository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * Set the search criteria.
     *
     * @param string $q
     *
     * @return self
     */
    public function search($q)
    {
        /* @var \Doctrine\ODM\MongoDB\Query\Builder $qb */
        $qb = $this->repository->createQueryBuilder();
        $q && $qb->field('title')->equals(new Regex('/' . addslashes($q) . '/i'));

        $adapter = new DoctrineMongoAdapter($qb);
        parent::__construct($adapter);
        return $this;
    }
}
