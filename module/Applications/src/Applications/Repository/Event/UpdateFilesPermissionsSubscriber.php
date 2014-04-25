<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** JobReferencesUpdateListener.php */ 
namespace Applications\Repository\Event;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Applications\Entity\ApplicationInterface;

class UpdateFilesPermissionsSubscriber implements EventSubscriber
{
    
    public function getSubscribedEvents()
    {
        return array(Events::onFlush);
    }
    
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $dm  = $eventArgs->getDocumentManager();
        $uow = $dm->getUnitOfWork();
        
        $filter    = function($element) { 
            return $element instanceOf ApplicationInterface
                   && $element->getPermissions()->hasChanged(); 
        };
        
        $inserts   = $uow->getScheduledDocumentInsertions();
        $updates   = $uow->getScheduledDocumentUpdates();
        $documents = array_merge(array_filter($inserts, $filter), array_filter($updates, $filter));
        
        foreach ($documents as $document) {
            $permissions = $document->getPermissions();
           
            foreach ($document->getAttachments() as $attachment) {
                $attachment->getPermissions()
                           ->clear()
                           ->inherit($permissions);
                $uow->recomputeSingleDocumentChangeSet(
                    $dm->getClassMetadata(get_class($attachment)), $attachment
                );
            }
            
            if ($image = $document->contact->image) {
                $image->getPermissions()
                      ->clear()
                      ->inherit($permissions);
                $uow->recomputeSingleDocumentChangeSet(
                    $dm->getClassMetadata(get_class($image)), $image
                );
            }
        }
    }
}

