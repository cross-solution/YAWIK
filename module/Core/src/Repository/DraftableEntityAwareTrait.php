<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Repository;

use Core\Entity\EntityInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * Enables an implementing repository to handle draftable entities.
 *
 * @method EntityInterface create(array $data = [], bool $persist = false)
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
trait DraftableEntityAwareTrait
{
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null): array
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return parent::findBy($criteria, $sort, $limit, $skip);
    }

    /**
     * Find entities in draft mode.
     *
     * Sets the key 'isDraft' with the value true and proxies to parent::findBy()
     *
     * @param array $criteria
     * @param array|null $sort
     * @param null|int  $limit
     * @param null|int  $skip
     *
     * @return \MongoCursor
     */
    public function findDraftsBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        $criteria['isDraft'] = true;

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return parent::findBy($criteria, $sort, $limit, $skip);
    }

    public function findOneBy(array $criteria, ?array $sort = null): ?object
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return parent::findOneBy($criteria);
    }

    /**
     * Finds one entity in draft mode.
     *
     * Sets the key 'isDraft' to true and proxies to parent::findOneBy()
     *
     * @param array $criteria
     *
     * @return \MongoCursor
     */
    public function findOneDraftBy(array $criteria): ?object
    {
        $criteria['isDraft'] = true;
        return parent::findOneBy($criteria);
    }

    /**
     * Creates a query builder.
     *
     * Prepopulate the constraint for 'isDraft' according to $findDrafts:
     * - true: queryBuilder will return only drafts
     * - false: queryBuilder will return only non drafts.
     * - null: queryBuilder will return all entities matching the additional constraints.
     *
     * @param bool|null $findDrafts
     *
     * @return Builder
     */
    public function createQueryBuilder($findDrafts = false): Builder
    {
        $qb = parent::createQueryBuilder();

        if (null !== $findDrafts) {
            $qb->field('isDraft')->equals($findDrafts);
        }
        return $qb;
    }

    /**
     * Creates an entity in draft mode.
     *
     * @param array $data
     * @param bool  $persist
     *
     * @return EntityInterface
     */
    public function createDraft(array $data = null, $persist = false)
    {
        $data['isDraft'] = true;

        return $this->create($data, $persist);
    }
}
