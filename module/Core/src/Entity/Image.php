<?php

declare(strict_types=1);

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Image
 *
 * @ODM\File(bucketName="core.fs.images")
 * @package Core\Entity
 */
class Image implements ImageInterface
{
    use FileTrait;

    /**
     * @ODM\File\Metadata(targetDocument="Core\Entity\ImageMetadata")
     */
    protected ?ImageMetadata $metadata = null;

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getUri(): string
    {
        return '/'.trim('file/Core.Images/' . $this->id . "/" . urlencode($this->name), '/');
    }
}