<?php

namespace Cv\Repository;

use Core\Repository\AbstractRepository;
use Auth\Entity\UserInterface;
use Applications\Entity\Application;
use Cv\Entity\Cv as CvEntity;

/**
 * class for accessing CVs
 *
 * @method CvEntity create(array $data = null, $persist = false)
 */
class Cv extends AbstractRepository
{
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

        $document = $this->findOneBy(
            array(
                'isDraft' => true,
                'user' => $user
            )
        );

        if (!empty($document)) {
            return $document;
        }

        return null;
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
        $contact = $application->getContact();
        $contactImage = $contact->getImage();
        
        if ($contactImage)
        {
            $contactImage->setUser($user);
        }
        
        $cv->setContact($contact);
        
        $applicationAttachments = $application->getAttachments();
        
        if (count($applicationAttachments) > 0)
        {
            $cvAttachments = [];
        
            /* @var $applicationAttachment \Applications\Entity\Attachment */
            foreach ($applicationAttachments as $applicationAttachment)
            {
                $file = new \Doctrine\MongoDB\GridFSFile();
                $file->setBytes($applicationAttachment->getContent());
                
                $cvAttachment = new \Cv\Entity\Attachment();
                $cvAttachment->setName($applicationAttachment->getName());
                $cvAttachment->setType($applicationAttachment->getType());
                $cvAttachment->setPermissions($cvAttachment->getPermissions());
                $cvAttachment->setUser($user);
                $cvAttachment->setFile($file);
                $cvAttachment->setDateUploaded($applicationAttachment->getDateUploaded());
                
                $cvAttachments[] = $cvAttachment;
            }
            
            $cv->setAttachments(new \Doctrine\Common\Collections\ArrayCollection($cvAttachments));
        }
        
        return $cv;
    }
}
