<?php

declare(strict_types=1);

namespace Core\Entity;


/**
 * Class ImageMetadata
 *
 * @ODM\EmbeddedDocument
 * @package Core\Entity
 */
interface ImageMetadataInterface
{
    public function getOwnerFileClass(): string;

    /**
     * @return string|null
     */
    public function getBelongsTo(): ?string;

    public function setBelongsTo(?string $belongsTo);

    /**
     * @return string|null
     */
    public function getKey(): ?string;

    public function setKey(?string $key);
}