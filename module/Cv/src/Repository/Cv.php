<?php

namespace Cv\Repository;

use Core\Entity\PermissionsInterface;
use Core\Repository\DraftableEntityAwareInterface;
use Core\Repository\DraftableEntityAwareTrait;
use Core\Repository\AbstractRepository;
use Auth\Entity\UserInterface;
use Applications\Entity\Application;
use Cv\Entity\Cv as CvEntity;

/**
 * class for accessing CVs
 *
 * @method CvEntity create(array $data = null, $persist = false)
 */
class Cv extends AbstractRepository implements DraftableEntityAwareInterface
{
    use DraftableEntityAwareTrait;

    /**
     * Look for an drafted Document of a given user
     *
     * @param $user
     * @return CvEntity|null
     */
    public function findDraft($user)
    {
        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        return $this->findOneDraftBy(['user' => $user]);
    }
    
    /**
     * @param Application $application
     * @param UserInterface $user
     * @return CvEntity
     * @since 0.26
     */
    public function createFromApplication(Application $application, UserInterface $user)
    {
        $cv = $this->create();
        $cv->setContact($application->getContact());

        $assignedUser = $application->getJob()->getUser();
        $cv->setUser($assignedUser);

        $perms = $cv->getPermissions();

        $perms->inherit($application->getPermissions());
        // grant view permission to the user that issued this creation.
        $perms->grant($user, PermissionsInterface::PERMISSION_VIEW);
        // revoke change permission to the original applicant
        $perms->revoke($application->getUser(), PermissionsInterface::PERMISSION_CHANGE);
        
        $applicationAttachments = $application->getAttachments();
        
        if (count($applicationAttachments) > 0) {
            $cvAttachments = [];
        
            /* @var $applicationAttachment \Applications\Entity\Attachment */
            foreach ($applicationAttachments as $applicationAttachment) {
                $file = new \Doctrine\MongoDB\GridFSFile();
                $file->setBytes($applicationAttachment->getContent());
                
                $cvAttachment = new \Cv\Entity\Attachment();
                $cvAttachment->setName($applicationAttachment->getName());
                $cvAttachment->setType($applicationAttachment->getType());
                $cvAttachment->setUser($assignedUser);
                $cvAttachment->setFile($file);
                $cvAttachment->setDateUploaded($applicationAttachment->getDateUploaded());
                
                $cvAttachments[] = $cvAttachment;
            }
            
            $cv->setAttachments(new \Doctrine\Common\Collections\ArrayCollection($cvAttachments));
        }
        
        return $cv;
    }
}
