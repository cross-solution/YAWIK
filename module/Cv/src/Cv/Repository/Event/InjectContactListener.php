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
                    'contact.image' => new \MongoId($image->getId())
                ]);
            if ($cv) {
                $image->setContact($cv->getContact());
            }
        }
    }
}
