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
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Core\Entity\PreUpdateAwareEntityInterface;

class PreUpdateNotifier implements EventSubscriber
{

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceOf PreUpdateAwareEntityInterface) {
            $document->preUpdate();
        }
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate);
    }
	

}

