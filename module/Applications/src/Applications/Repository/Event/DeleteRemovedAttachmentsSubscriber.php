<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** JobReferencesUpdateListener.php */ 
namespace Applications\Repository\Event;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Applications\Entity\ApplicationInterface;
use Applications\Entity\Attachment;

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
     * @param OnFlushEventArgs $eventArgs
     * @return boolean
     */
    public function postRemoveEntity(LifecycleEventArgs $eventArgs)
    {
        $file = $eventArgs->getDocument();
        if (!$file instanceOf Attachment) {
            return;
        }
        
        $dm     = $eventArgs->getDocumentManager();
        $repo   = $dm->getRepository('Applications\Entity\Application');
        $fileId = new \MongoId($file->id); 
        
        foreach ($repo->findBy(array('attachments' => $fileId)) as $document) {
            $attachments = $document->getAttachments();
            $attachments->removeElement($file);
        }
        foreach ($repo->findBy(array('contact.image' => $fileId)) as $document) {
            $document->contact->image = null;
        }
    }
    
}

