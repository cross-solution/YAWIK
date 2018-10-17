<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** RepositoryCreated.php */
namespace Core\Repository\DoctrineMongoODM\Event;

use Doctrine\Common\EventSubscriber;
use Core\Entity\PreUpdateAwareInterface;
use Doctrine\ODM\MongoDB\Events;

class PreUpdateDocumentsSubscriber implements EventSubscriber
{
    /**
     * is just called in case of a new Entity
     *
     * @param Doctrine\ODM\MongoDB\Event\LifecycleEventArgs $eventArgs
     */
    public function prePersist($eventArgs)
    {
        $this->preUpdate($eventArgs, true);
    }
    
    /**
     *
     * @param Doctrine\ODM\MongoDB\Event\LifecycleEventArgs $eventArgs
     * @param boole $prePersist
     * @return type
     */
    public function preUpdate($eventArgs, $prePersist = false)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof PreUpdateAwareInterface) {
            return;
        }
        
        $document->preUpdate($prePersist);
        
        if (!$prePersist) {
            $dm         = $eventArgs->getDocumentManager();
            $uow       = $dm->getUnitOfWork();
            $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($document)), $document);
        }
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate, Events::prePersist);
    }
}
