<?php

declare(strict_types=1);

namespace Core\Entity;

use Auth\Entity\AnonymousUser;
use Auth\Entity\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait FileMetadataTrait
{
    /**
     * @ODM\Field(type="string", nullable=true)
     */
    protected ?string $contentType = null;

    /**
     * owner of an attachment. Typically this is the candidate who applies for a job offer.
     *
     * @ODM\ReferenceOne(targetDocument="Auth\Entity\User", storeAs="id", cascade={"persist"})
     */
    protected ?UserInterface $user = null;

    /**
     * @ODM\Field(type="string", nullable=true)
     */
    protected ?string $userToken;

    protected ?AnonymousUser $anonymousUser;

    /**
     * @ODM\EmbedOne(targetDocument="Core\Entity\Permissions")
     */
    protected ?PermissionsInterface $permissions = null;

    /**
     * @ODM\Field(type="string", nullable=true)
     */
    protected ?string $name = null;

    /**
     * @return string
     */
    public function getResourceId()
    {
        return 'Entity/File';
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @param string|null $contentType
     * @return $this
     */
    public function setContentType(?string $contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if (!$this->user && $this->userToken) {
            $this->user = new AnonymousUser($this->userToken);
        }

        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);

        if ($user instanceof AnonymousUser) {
            $this->userToken = $user->getToken();
        } elseif ($this->userToken) {
            $this->userToken = null;
        }

        return $this;
    }

    /**
     *
     * @ODM\PrePersist
     * @ODM\PreUpdate
     * @ODM\PreFlush
     */
    public function preventPersistingAnonymousUser(): void
    {
        if ($this->user instanceof AnonymousUser) {
            $this->anonymousUser = $this->user;
            $this->user = null;
        }
    }

    /**
     *
     * @ODM\PostPersist
     * @ODM\PostUpdate
     */
    public function restoreAnonymousUser(): void
    {
        if ($this->anonymousUser) {
            $this->user = $this->anonymousUser;
            $this->anonymousUser = null;
        }
    }

    /**
     * @return PermissionsInterface|null
     */
    public function getPermissions(): ?PermissionsInterface
    {
        if (!$this->permissions) {
            $perms = new Permissions();
            $user = $this->getUser();
            if ($user instanceof UserInterface) {
                $perms->grant($user, PermissionsInterface::PERMISSION_ALL);
            }
            $this->setPermissions($perms);
        }
        return $this->permissions;
    }

    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }
}