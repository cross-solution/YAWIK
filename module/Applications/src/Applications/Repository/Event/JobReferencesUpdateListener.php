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
use Jobs\Entity\JobInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;

class JobReferencesUpdateListener implements EventSubscriber
{
    
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate);
    }
    
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceOf JobInterface) {
            return;
        }
        $dm = $eventArgs->getDocumentManager();
        $changeset = $dm->getUnitOfWork()->getDocumentChangeset($document);
        
        if (!isset($changeset['user'])) {
            return;
        }
        
        $userId = $document->getUser()->getId();

        $dm->createQueryBuilder('Applications\Entity\Application')
            ->update()->multiple(true)
            ->field('refs.jobs.userId')->set($userId)
            ->field('refs.jobs.__id__')->equals($document->getId())
            ->field('refs.jobs.userId')->notEqual($userId)
            ->getQuery()
            ->execute();
    }
}

