<?php

declare(strict_types=1);

namespace Cv\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\ImageMetadata;

/**
 * Class ContactImageMetadata
 * @ODM\EmbeddedDocument
 * @ODM\HasLifecycleCallbacks()
 * @package Cv\Entity
 */
class ContactImageMetadata extends ImageMetadata
{
    /**
     * @var Contact
     */
    protected Contact $contact;

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * @ODM\PreRemove
     */
    public function preRemove()
    {
        $this->contact->setImage(null);
    }
}