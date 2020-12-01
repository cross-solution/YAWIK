<?php

declare(strict_types=1);

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ImageMetadata
 *
 * @ODM\EmbeddedDocument
 * @package Core\Entity
 */
class ImageMetadata implements FileMetadataInterface
{
    use EntityTrait, FileMetadataTrait;

    /**
     * @ODM\Field(type="string", nullable=true)
     */
    protected ?string $belongsTo = null;

    /**
     * @ODM\Field(type="string", nullable=true)
     */
    protected ?string $key = null;

    public function getOwnerFileClass(): string
    {
        return Image::class;
    }

    /**
     * @return string|null
     */
    public function getBelongsTo(): ?string
    {
        return $this->belongsTo;
    }

    public function setBelongsTo(?string $belongsTo)
    {
        $this->belongsTo = $belongsTo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key)
    {
        $this->key = $key;
        return $this;
    }
}