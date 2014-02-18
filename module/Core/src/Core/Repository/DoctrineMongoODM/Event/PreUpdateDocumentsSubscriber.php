<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** RepositoryCreated.php */ 
namespace Core\Repository\DoctrineMongoODM\Event;

use Doctrine\Common\EventSubscriber;
use Core\Entity\PreUpdateAwareInterface;
use Doctrine\ODM\MongoDB\Events;

class PreUpdateDocumentsSubscriber implements EventSubscriber
{
    public function prePersist($eventArgs) 
    {
        $this->preUpdate($eventArgs, true);
    }
    
    public function preUpdate($eventArgs, $prePersist = false)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceOf PreUpdateAwareInterface) {
            return;
        }
        
        if (false !== $document->preUpdate($prePersist) && !$prePersist) {
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

