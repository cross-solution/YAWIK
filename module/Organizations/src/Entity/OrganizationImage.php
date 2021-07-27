<?php

declare(strict_types=1);

namespace Organizations\Entity;

use Core\Entity\EntityTrait;
use Core\Entity\FileTrait;
use Core\Entity\ImageInterface;
use Core\Entity\ImageMetadata;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Image
 *
 * @ODM\File(bucketName="organizations.images")
 * @package Organizations\Entity
 */
class OrganizationImage implements ImageInterface
{
    use FileTrait, EntityTrait;

    /**
     * @ODM\File\Metadata(targetDocument="Organizations\Entity\OrganizationImageMetadata")
     */
    protected ?ImageMetadata $metadata = null;

    public function getMetadata(): ?ImageMetadata
    {
        return $this->metadata;
    }

    public function getUri(): string
    {
        $id = (string)$this->id;
        $name = (string)$this->name;
        return '/'.trim('file/Organizations.OrganizationImage/' . $id . "/" . urlencode($name), '/');
    }

}