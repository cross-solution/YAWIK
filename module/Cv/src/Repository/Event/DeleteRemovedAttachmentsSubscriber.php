<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Repository\Event;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Cv\Entity\Attachment;
use MongoDB\BSON\ObjectId;

/**
 * Subscriber for deleting CV attachment references
 *
 * @author fedys
 * @since 0.26
 */
class DeleteRemovedAttachmentsSubscriber implements EventSubscriber
{
    /**
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return array('postRemoveEntity');
    }
    
    /**
     * Removes attachments
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemoveEntity(LifecycleEventArgs $eventArgs)
    {
        $file = $eventArgs->getDocument();
        if (!$file instanceof Attachment) {
            return;
        }
        
        $fileId = new ObjectId($file->getId());
        $dm = $eventArgs->getDocumentManager();
        $dm->createQueryBuilder('Cv\Entity\Cv')
            ->updateMany()
            ->field('attachments')->equals($fileId)->pull($fileId)
            ->getQuery()
            ->execute();
    }
}
