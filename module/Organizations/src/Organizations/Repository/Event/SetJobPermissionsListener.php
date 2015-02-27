<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Repository\Event;

use Core\Entity\PermissionsInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Jobs\Entity\JobInterface;
use Organizations\Entity\EmployeePermissionsInterface;

/**
 * Updates permissions on job entities according to the settings in the
 * organization assigned to the job.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 * @since 0.18
 */
class SetJobPermissionsListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate, Events::prePersist);
    }

    /**
     * PreUpdate hook.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->setPermissions($args, true);
    }

    /**
     * PrePersist hook.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setPermissions($args, false);
    }

    /**
     * Sets jobs permissions.
     *
     * @param LifecycleEventArgs $args
     * @param boolean $update if true, the changeset of the document will be recomputed.
     */
    protected function setPermissions($args, $update)
    {
        $document = $args->getDocument();

        if (!$document instanceOf JobInterface)  {
            return;
        }

        $organization = $document->getOrganization();
        if ($organization->isHiringOrganization()) {
            $organization = $organization->getParent();
        }

        /* @var $perms \Core\Entity\Permissions */
        $employees = $organization->getEmployees();
        $user = $document->getUser();
        $perms = $document->getPermissions();

        $perms->grant($organization->getOwner(), PermissionsInterface::PERMISSION_ALL, false);
        foreach ($employees as $emp) {
            /* @var $emp \Organizations\Entity\Employee */
            $empPerm = $emp->getPermissions();
            $empUser = $emp->getUser();
            if ($empPerm->isAllowed(EmployeePermissionsInterface::JOBS_CHANGE)
                || ($empPerm->isAllowed(EmployeePermissionsInterface::JOBS_CREATE) && $user->getId() == $empUser->getId())
            ) {
                $perms->grant($empUser, PermissionsInterface::PERMISSION_CHANGE, false);
            } else if ($empPerm->isAllowed(EmployeePermissionsInterface::JOBS_VIEW)) {
                $perms->grant($empUser, PermissionsInterface::PERMISSION_VIEW, false);
            }
        }
        $perms->build();

        if ($update) {
            $document = $args->getDocument();
            $dm       = $args->getDocumentManager();
            $uow      = $dm->getUnitOfWork();
            $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($document)), $document);
        }
    }

}