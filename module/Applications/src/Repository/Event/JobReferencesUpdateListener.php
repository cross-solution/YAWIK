<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** JobReferencesUpdateListener.php */
namespace Applications\Repository\Event;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Jobs\Entity\JobInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\JobSnapshot;

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
        $userChanged = isset($changeset['user']) && $changeset['user'][0] !== $changeset['user'][1];
        $managersChanged =
            //!$document instanceof JobSnapshot
             isset($changeset['metaData'])
            && (isset($changeset['metaData'][1]['organizations:managers'])
                || isset($changeset['metaData'][0]['organizations:managers']))
        ;
        if (!$userChanged && !$managersChanged) {
            return;
        }

        /* User could have gotten unset! */
        if ($userChanged) {
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

        if ($managersChanged) {
            $managers = [];
            if (isset($changeset['metaData'][1]['organizations:managers'])) {
                foreach ($changeset['metaData'][1]['organizations:managers'] as $man) {
                    $managers[] = $man['id'];
                }
            }

            $dm->createQueryBuilder('Applications\Entity\Application')
                ->update()->multiple(true)
                ->field('refs.jobManagers')->set($managers)
                ->field('job')->equals($document->getId())
                ->getQuery()
                ->execute();
        }
    }
}
