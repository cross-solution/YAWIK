<?php

declare(strict_types=1);

namespace Core\Entity;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

trait FileTrait
{
    /**
     * @ODM\Id()
     */
    protected ?string $id = null;

    /**
     * @ODM\File\Filename()
     */
    protected ?string $name = null;

    /**
     * @ODM\File\UploadDate(type="tz_date")
     */
    protected ?DateTimeInterface $uploadDate = null;

    /**
     * @ODM\File\Length
     */
    protected ?int $length = null;

    /**
     * @ODM\File\ChunkSize
     */
    protected ?int $chunkSize = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getUploadDate(): ?DateTimeInterface
    {
        return $this->uploadDate;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @return int|null
     */
    public function getChunkSize(): ?int
    {
        return $this->chunkSize;
    }

    /**
     * Gets the length of file in GB, MB ot kB format
     *
     * @return string
     */
    public function getPrettySize(): string
    {
        $size = $this->getLength();

        if ($size >= 1073741824) {
            return round($size / 1073741824, 2) . ' GB';
        }

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }

        if ($size >= 1024) {
            return round($size / 1024, 2) . ' kB';
        }

        return (string)$size;
    }
}