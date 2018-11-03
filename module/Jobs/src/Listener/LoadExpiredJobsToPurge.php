<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Listener;

use Core\Service\EntityEraser\LoadEvent;
use Jobs\Entity\StatusInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class LoadExpiredJobsToPurge
{
    const EVENT_NAME = 'expired-jobs';

    public function __invoke(LoadEvent $event)
    {
        /* @var \Jobs\Repository\Job $repository */
        $days = $event->getParam('days', 80);
        $date = new \DateTime("- $days days");
        $repository = $event->getRepository('Jobs');
        $qb = $repository->createQueryBuilder();
        $qb->field('status.name')->notEqual(StatusInterface::ACTIVE);
        $qb->addOr(
            $qb->expr()->addAnd(
                $qb->expr()->field('datePublishEnd')->exists(true),
                $qb->expr()->field('datePublishEnd.date')->lt($date)
            ),
            $qb->expr()->addAnd(
                $qb->expr()->field('datePublishEnd')->exists(false),
                $qb->expr()->field('dateCreated.date')->lt($date)
            )
        )->limit($event->getParam('limit', 0));
        $entities = $qb->getQuery()->execute()->toArray();

        return $entities;
    }

    public function onFetchList(LoadEvent $event)
    {
        return [
            'key' => self::EVENT_NAME,
            'options' => [
                'days' => 'Amount of days that must have passed beyond the publishEndDate (default 80)',
                'limit' => 'Maximum amount of jobs to process.'
            ],
            'description' => 'Purges all expired jobs older than a specified range.'
        ];
    }
}
