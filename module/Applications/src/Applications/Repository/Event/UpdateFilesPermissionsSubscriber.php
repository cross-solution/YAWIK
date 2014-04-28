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
        
        $filter = function($element) { 
            return $element instanceOf ApplicationInterface
                   && $element->getPermissions()->hasChanged(); 
        };
        
        $inserts = array_filter($uow->getScheduledDocumentInsertions(), $filter);
        $updates = array_filter($uow->getScheduledDocumentUpdates(), $filter);
        
        $this->updateFilesPermissions($inserts, /* isUpdate */ false);
        $this->updateFilesPermissions($updates, /* isUpdate */ true);
        
    }
    
    protected function updateFilesPermissions($documents, $isUpdate = false)
    {
        foreach ($documents as $document) {
            $permissions = $document->getPermissions();
           
            foreach ($document->getAttachments() as $attachment) {
                $attachment->getPermissions()
                           ->clear()
                           ->inherit($permissions);
                if ($isUpdate) {
                    $uow->computeChangeSet(
                        $dm->getClassMetadata(get_class($attachment)), $attachment
                    );
                }
            }
            
            if ($image = $document->contact->image) {
                $image->getPermissions()
                      ->clear()
                      ->inherit($permissions);
                if ($isUpdate) {
                    $uow->computeChangeSet(
                        $dm->getClassMetadata(get_class($image)), $image
                    );
                }
            }
        }
    }
}

