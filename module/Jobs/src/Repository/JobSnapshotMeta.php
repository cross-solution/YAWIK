<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Repository;

use Core\Repository\AbstractRepository;
use Jobs\Entity\JobInterface;

class JobSnapshotMeta extends AbstractRepository
{
    const SNAPSHOT_LAST = 'last';

    /**
     * @param JobInterface $jobEntity
     * @param string $find
     * @return array
     */
    public function findSnapshot(JobInterface $jobEntity, $find = self::SNAPSHOT_LAST)
    {
        $qb = $this->createQueryBuilder();
        $qb->hydrate(true)
           ->select('entity')
           ->field('sourceId')->equals($jobEntity->id)
           ->sort('dateCreated', 'desc')
           ->limit(1);

        $result = $qb->getQuery()->execute();
        if ($result->count() == 0) {
            return array();
        }
        $result->rewind();
        $data = $result->current();
        return $data->entity;
    }

    /**
     * @param JobInterface $jobEntity
     * @param string $exclude
     * @return mixed
     */
    public function removeSnapshots(JobInterface $jobEntity, $exclude = self::SNAPSHOT_LAST)
    {
        // there is apparently no way to remove the second and following documents.
        // Removes are always removing all matches.
        // So we need to create a cursor, that skips the first Document
        $qb = $this->createQueryBuilder();
        $qb->hydrate(false)
           ->select('entity')
           ->field('sourceId')->equals($jobEntity->id)
           ->sort('dateCreated', 'desc');
        $result = $qb->getQuery()->execute()->skip(1);
        foreach ($result as $item) {
            $id = $item['_id'];
            $qbItem = $this->createQueryBuilder();
            $qbItem->remove()
                ->field('_id')->equals($id)
                ->getQuery()->execute();
        }
        return $result;
    }
}
