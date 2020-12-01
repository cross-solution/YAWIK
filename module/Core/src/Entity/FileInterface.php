<?php

declare(strict_types=1);

namespace Core\Entity;

use DateTimeInterface;

/**
 * Interface FileMetadataInterface
 *
 * @author Anthonius Munthi
 *
 * @since 0.36
 * @package Core\Entity
 */
interface FileInterface
{
    public function getId(): ?string;

    public function getName(): ?string;

    public function getChunkSize(): ?int;

    public function getLength(): ?int;

    public function getUploadDate(): ?DateTimeInterface;

    public function getUri(): string;

    public function getPrettySize(): string;

    /**
     * @return FileMetadataInterface|object
     */
    public function getMetadata();
}