<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Paginator;

use Core\Paginator\Adapter\DoctrineMongoCursor;
use Zend\Paginator\Paginator;

/**
 * Paginator for Job title select element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelectPaginator extends Paginator
{
    /**
     * @var \Jobs\Repository\Job
     */
    private $repository;
    
    public function __construct($repository)
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
        $qb->field('title')->equals(new \MongoRegex('/' . addslashes($q) . '/i'));
        $cursor = $qb->getQuery()->execute();

        $adapter = new DoctrineMongoCursor($cursor);
        parent::__construct($adapter);

        return $this;
    }
}
