<?php

declare(strict_types=1);

namespace Yawik\Migration\Entity;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="migration")
 */
class Migration
{
    /**
     * @ODM\Id
     */
    private string $id;

    /**
     * @ODM\Field(type="string")
     */
    private string $class;

    /**
     * @ODM\Field(type="string")
     */
    private string $version;

    /**
     * @ODM\Field(type="string")
     */
    private string $description;

    /**
     * @ODM\Field(type="bool")
     */
    private bool $migrated;

    /**
     * @ODM\Field(type="date")
     */
    private ?DateTimeInterface $migratedAt;

    public function __construct(
        string $class,
        string $version,
        string $description,
        bool $migrated = false,
        ?DateTimeInterface $migratedAt = null
    )
    {
        $this->class = $class;
        $this->version = $version;
        $this->migrated = $migrated;
        $this->migratedAt = $migratedAt;
        $this->description = $description;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function isMigrated(): bool
    {
        return $this->migrated;
    }

    public function setMigrated(bool $state)
    {
        $this->migrated = $state;
    }

    public function setMigratedAt(\DateTimeInterface $date)
    {
        $this->migratedAt = $date;
    }

    public function getMigratedAt(): ?DateTimeInterface
    {
        return $this->migratedAt;
    }
}