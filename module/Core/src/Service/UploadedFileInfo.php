<?php

declare(strict_types=1);

namespace Core\Service;


class UploadedFileInfo
{
    private string $contentType;
    private string $name;
    private string $tmpFile;

    public function __construct(
        string $name,
        string $contentType,
        string $tmpFile
    )
    {
        $this->contentType = $contentType;
        $this->name = $name;
        $this->tmpFile = $tmpFile;
    }

    public static function fromArray(array $data)
    {
        return new self($data['name'], $data['type'], $data['tmp_name']);
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTmpFile(): string
    {
        return $this->tmpFile;
    }

    public function getImageFormat(): ?string
    {
        $format = null;
        if(!is_null($this->contentType)){
            $format = str_replace('image/', '', $this->contentType);
        }
        return $format;
    }
}