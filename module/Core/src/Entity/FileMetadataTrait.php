<?php

declare(strict_types=1);

namespace Core\Entity;


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
     * @ODM\EmbedOne(targetDocument="Core\Entity\Permissions")
     */
    protected ?PermissionsInterface $permissions = null;

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
        return $this->user;
    }

    public function setUser(UserInterface $user)
    {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);

        return $this;
    }

    /**
     * @return PermissionsInterface|null
     */
    public function getPermissions(): ?PermissionsInterface
    {
        if (!$this->permissions) {
            $perms = new Permissions();
            if ($this->user instanceof UserInterface) {
                $perms->grant($this->user, PermissionsInterface::PERMISSION_ALL);
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
}