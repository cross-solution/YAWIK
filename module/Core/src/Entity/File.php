<?php

declare(strict_types=1);

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class File
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.36
 * @package Core\Entity
 */
abstract class File implements FileInterface
{
    use FileTrait;

    /**
     * @ODM\File\Metadata(targetDocument="Core\Entity\FileMetadata")
     */
    protected FileMetadataInterface $metadata;

    public function setMetadata(FileMetadataInterface $metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}