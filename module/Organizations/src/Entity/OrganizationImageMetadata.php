<?php

declare(strict_types=1);

namespace Organizations\Entity;

use Core\Entity\ImageMetadata as BaseImageMetadata;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Image
 *
 * @ODM\EmbeddedDocument
 * @package Organizations\Entity
 */
class OrganizationImageMetadata extends BaseImageMetadata
{
    /**
     * Organization which belongs to the company logo
     * @ODM\ReferenceOne(targetDocument="Organizations\Entity\Organization", storeAs="id")
     */
    protected ?Organization $organization;

    public function getOwnerFileClass(): string
    {
        return OrganizationImage::class;
    }

    /**
     * @return Organization|null
     */
    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }
}