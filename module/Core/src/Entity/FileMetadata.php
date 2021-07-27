<?php

declare(strict_types=1);

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class FileMetadata
 *
 * @ODM\EmbeddedDocument
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.36
 * @package Core\Entity
 */
class FileMetadata implements FileMetadataInterface
{
    use FileMetadataTrait;
}