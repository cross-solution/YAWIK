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
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Applications\Entity\ApplicationInterface;

/**
 * class for updating file permissions
 */
class UpdateFilesPermissionsSubscriber implements EventSubscriber
{
    /**
     * Gets events
     *
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents(): array
    {
        return array(Events::onFlush);
    }

    /**
     * Updates fiile permissions on Flush
     *
     * @param OnFlushEventArgs $eventArgs
     * @return void
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $dm  = $eventArgs->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        $filter = function ($element) {
            return $element instanceof ApplicationInterface
                   && $element->getPermissions()->hasChanged();
        };

        $inserts = array_filter($uow->getScheduledDocumentInsertions(), $filter);
        $updates = array_filter($uow->getScheduledDocumentUpdates(), $filter);

        foreach (array($inserts, $updates) as $isUpdate => $documents) {
            foreach ($documents as $document) { /* @var \Applications\Entity\Application $document */
                $permissions = $document->getPermissions();

                foreach ($document->getAttachments() as $attachment) {
                    /* @var \Applications\Entity\Attachment $attachment */
                    $attachment->getMetadata()
                               ->getPermissions()
                               ->clear()
                               ->inherit($permissions);
                    if ($isUpdate) {
                        $uow->computeChangeSet(
                            $dm->getClassMetadata(get_class($attachment)),
                            $attachment
                        );
                    }
                }

                if ($image = $document->getContact()->getImage()) {
                    $image->getMetadata()
                        ->getPermissions()
                        ->clear()
                        ->inherit($permissions);
                    if ($isUpdate) {
                        $uow->computeChangeSet(
                            $dm->getClassMetadata(get_class($image)),
                            $image
                        );
                    }
                }
            }
        }
    }
}
