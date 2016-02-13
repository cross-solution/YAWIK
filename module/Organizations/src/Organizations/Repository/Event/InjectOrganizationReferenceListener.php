<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Repository\Event;

use Auth\Entity\UserInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Organizations\Entity\OrganizationReference;

/**
 * This listener creates and injects organization references to user entities.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class InjectOrganizationReferenceListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(Events::postLoad);
    }

    /**
     * Creates and injects the organization reference to an user entity.
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();

        if ($document instanceof UserInterface) {
            $repository = $args->getDocumentManager()->getRepository('Organizations\Entity\Organization');
            $userId  = $document->getId();
            $reference = new OrganizationReference($userId, $repository);

            $document->setOrganization($reference);
        }
    }
}
