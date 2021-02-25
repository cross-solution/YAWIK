<?php

declare(strict_types=1);

namespace Core\Entity;

use Auth\Entity\UserInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Interface FileMetadataInterface
 *
 * @author Anthonius Munthi
 *
 * @since 0.36
 * @package Core\Entity
 */
interface FileMetadataInterface extends ResourceInterface
{
    public function getName(): ?string;

    public function setUser(?UserInterface $user): self;

    public function getUser(): ?UserInterface;

    public function setPermissions(PermissionsInterface $permissions);

    public function getPermissions(): ?PermissionsInterface;

    /**
     * @param string $contentType
     * @return self
     */
    public function setContentType(string $contentType): self;

    public function getContentType(): ?string;
}