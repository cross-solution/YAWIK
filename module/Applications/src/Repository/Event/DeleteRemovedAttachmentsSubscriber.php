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
use Applications\Entity\Attachment;
use MongoDB\BSON\ObjectId;

/**
 * class for deleting attachment references.
 */
class DeleteRemovedAttachmentsSubscriber implements EventSubscriber
{
    /**
     * Gets events
     *
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array('postRemoveEntity');
    }

    /**
     * Updates fiile permissions on Flush
     *
     * @param LifecycleEventArgs $eventArgs
     * @return void
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * @throws \MongoException
     */
    public function postRemoveEntity(LifecycleEventArgs $eventArgs): void
    {
        $file = $eventArgs->getDocument();
        if (!$file instanceof Attachment) {
            return;
        }

        $dm     = $eventArgs->getDocumentManager();
        //$repo   = $dm->getRepository('Applications\Entity\Application');

        $fileId = new ObjectId($file->getId());

        $dm->createQueryBuilder('Applications\Entity\Application')
           ->update()
            ->multiple(true)
           ->field('attachments')->equals($fileId)->pull($fileId)
           ->getQuery()->execute();


        $dm->createQueryBuilder('Applications\Entity\Application')
           ->update()
            ->multiple(true)
           ->field('contact.image')->equals($fileId)->set(null)
           ->getQuery()->execute();
    }
}
