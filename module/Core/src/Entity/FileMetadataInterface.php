<?php

declare(strict_types=1);

namespace Core\Entity;

use Auth\Entity\UserInterface;

/**
 * Interface FileMetadataInterface
 *
 * @author Anthonius Munthi
 *
 * @since 0.36
 * @package Core\Entity
 */
interface FileMetadataInterface
{
    public function setUser(UserInterface $user);

    public function getUser(): ?UserInterface;

    public function setPermissions(PermissionsInterface $permissions);

    public function getPermissions(): ?PermissionsInterface;

    /**
     * @param string $contentType
     * @return self
     */
    public function setContentType(string $contentType);

    public function getContentType(): ?string;
}