<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** JobReferencesUpdateListener.php */
namespace Applications\Repository\Event;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Jobs\Entity\JobInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;

/**
 * class for updating references
 */
class JobReferencesUpdateListener implements EventSubscriber
{
    /**
     * Gets list of subscribers
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate);
    }

    /**
     * updates references
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof JobInterface) {
            return;
        }
        $dm = $eventArgs->getDocumentManager();
        $changeset = $dm->getUnitOfWork()->getDocumentChangeset($document);


        /* Somehow it could be that a changeset is produced, where the user
         * is not actually changed - we check for it here */
        if (!isset($changeset['user']) || $changeset['user'][0] === $changeset['user'][1]) {
            return;
        }

        /* User could have gotten unset! */
        $user = $document->getUser();
        $userId = $user ? $user->getId() : null;

        $dm->createQueryBuilder('Applications\Entity\Application')
            ->update()->multiple(true)
            ->field('refs.jobs.userId')->set($userId)
            ->field('refs.jobs.__id__')->equals($document->getId())
            ->field('refs.jobs.userId')->notEqual($userId)
            ->getQuery()
            ->execute();
    }
}
