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
//use Jobs\Entity\JobInterface;
use Applications\Entity\Subscriber;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;

/**
 * class for updating references 
 */
class PostLoadSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(Events::postLoad);
    }
    
    public function postLoad($eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceOf Subscriber) {
            return;
        }
        $dm        = $eventArgs->getDocumentManager();
        $uow       = $dm->getUnitOfWork();
        $repository = $dm->getRepository('Applications\Entity\Subscriber');
        $document->injectRepository($repository);
    }
}