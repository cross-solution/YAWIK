<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
namespace Cv\Repository\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Cv\Entity\ContactImage;
use MongoDB\BSON\ObjectId;

/**
 * This listener injects contact reference to contact image
 */
class InjectContactListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(Events::postLoad);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $image = $args->getDocument();
        
        if ($image instanceof ContactImage) {
            $cv = $args->getDocumentManager()
                ->getRepository('Cv\Entity\Cv')
                ->findOneBy([
                    'contact.image' => new ObjectId($image->getId())
                ]);
            if ($cv) {
                $image->getMetadata()->setContact($cv->getContact());
            }
        }
    }
}
