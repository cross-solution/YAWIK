<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Permissions.php */
namespace Core\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;
use Core\Entity\Collection\ArrayCollection;

/**
 * Manages permissions for an entity.
 *
 *
 * @method boolean isAllGranted($userOrId)    shortcut for isGranted($userOrId, self::PERMISSION_ALL)
 * @method boolean isNoneGranted($userOrId)   shortcut for isGranted($userOrId, self::PERMISSION_NONE)
 * @method boolean isChangeGranted($userOrId) shortcut for isGranted($userOrId, self::PERMISSION_CHANGE)
 * @method boolean isViewGranted($userOrId)   shortcut for isGranted($userOrId, self::PERMISSION_VIEW)
 * @method $this grantAll($resource)          shortcut for grant($resource, self::PERMISSION_ALL)
 * @method $this grantNone($resource)         shortcut for grant($resource, self::PERMISSION_NONE)
 * @method $this grantChange($resource)       shortcut for grant($resource, self::PERMISSION_CHANGE)
 * @method $this grantView($resource)         shortcut for grant($resource, self::PERMISSION_VIEW)
 * @method $this revokeAll($resource)         shortcut for grant($resource, self::PERMISSION_ALL)
 * @method $this revokeNone($resource)        shortcut for grant($resource, self::PERMISSION_NONE)
 * @method $this revokeChange($resource)      shortcut for grant($resource, self::PERMISSION_CHANGE)
 * @method $this revokeView($resource)        shortcut for grant($resource, self::PERMISSION_VIEW)
 *
 * @ODM\EmbeddedDocument
 * @ODM\HasLifeCycleCallbacks
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Permissions implements PermissionsInterface
{
    /**
     * The type of this Permissions
     *
     * default is the Fully qualified class name.
     *
     * @ODM\Field(type="string")
     * @var string
     * @since 0,18
     */
    protected $type;

    /**
     * Ids of users, which have view access.
     *
     * @var array
     * @ODM\Field(type="collection")
     * @ODM\Index
     */
    protected $view = array();

    /**
     * Ids of users, which have change access.
     *
     * @var array
     * @ODM\Field(type="collection")
     * @ODM\Index
     */
    protected $change = array();
    
    /**
     * Specification of assigned resources.
     *
     * As of 0.18, the format is:
     * <pre>
     * array(
     *  resourceId => array(
     *    permission => array(userId,...),
     *    ...
     *  ),
     *  ...
     * );
     * </pre>
     *
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $assigned = array();
    
    /**
     * Collection of all assigned resources.
     *
     * @var Collection
     * @ODM\ReferenceMany(discriminatorField="_resource")
     */
    protected $resources;

    /**
     * Flag, wether this permissions has changed or not.
     *
     * @var bool
     */
    protected $hasChanged = false;

    /**
     * Creates a Permissions instance.
     *
     * @param string|null $type The type identifier, defaults to FQCN.
     */
    public function __construct($type = null)
    {
        $this->type = $type ?: get_class($this);
    }

    /**
     * Clones resources in a new ArrayCollection.
     * Needed because PHP does not deep cloning objects.
     * (That means, references stay references pointing to the same
     *  object than the parent.)
     */
    public function __clone()
    {
        $resources = new ArrayCollection();
        if ($this->resources) {
            foreach ($this->resources as $r) {
                $resources->add($r);
            }
        }
        $this->resources = $resources;
    }

    /**
     * Provides magic methods.
     *
     * - is[View|Change|None|All]Granted($user)
     * - grant[View|Change|None|All]($user)
     * - revoke[View|Change|None|All($user)
     *
     * @param string $method
     * @param array $params
     *
     * @return self|bool
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public function __call($method, $params)
    {
        if (1 != count($params)) {
            throw new \InvalidArgumentException('Missing required parameter.');
        }

        if (preg_match('~^is(View|Change|None|All)Granted$~', $method, $match)) {
            $permission = constant('self::PERMISSION_' . strtoupper($match[1]));
            return $this->isGranted($params[0], $permission);
        }
        
        if (preg_match('~^grant(View|Change|None|All)$~', $method, $match)) {
            $permission = constant('self::PERMISSION_' . strtoupper($match[1]));
            return $this->grant($params[0], $permission);
        }
        
        if (preg_match('~^revoke(View|Change|None|All)$~', $method, $match)) {
            $permission = constant('self::PERMISSION_' . strtoupper($match[1]));
            return $this->revoke($params[0], $permission);
        }
        
        throw new \BadMethodCallException('Unknown method "' . $method . '"');
    }

    /**
     * Gets the permission type
     *
     * @return string
     * @since 0.24
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Grants a permission to a user or resource.
     *
     * {@inheritDoc}
     *
     * @param bool $build Should the view and change arrays be rebuild?
     */
    public function grant($resource, $permission = null, $build = true)
    {
        if (is_array($resource)) {
            foreach ($resource as $r) {
                $this->grant($r, $permission, false);
            }
            if ($build) {
                $this->build();
            }
            return $this;
        }

        //new \Doctrine\ODM\MongoDB\
        true === $permission
        || (null === $permission && $resource instanceof PermissionsResourceInterface)
        || $this->checkPermission($permission);
        
        $resourceId = $this->getResourceId($resource);
        
        if (true === $permission) {
            $permission = $this->getFrom($resource);
        }
        
        if (self::PERMISSION_NONE == $permission) {
            if ($resource instanceof PermissionsResourceInterface) {
                $refs = $this->getResources();
                if ($refs->contains($resource)) {
                    $refs->removeElement($resource);
                }
            }
            unset($this->assigned[$resourceId]);
        } else {
            if ($resource instanceof PermissionsResourceInterface) {
                $spec = $resource->getPermissionsUserIds($this->type);
                if (!is_array($spec) || !count($spec)) {
                    $spec = array();
                } elseif (is_numeric(key($spec))) {
                    $spec = array($permission => $spec);
                }
            } else {
                $spec = array($permission => $resource instanceof UserInterface ? array($resource->getId()) : array($resource));
            }

            $this->assigned[$resourceId] = $spec;

            if ($resource instanceof PermissionsResourceInterface) {
                try {
                    $refs = $this->getResources();
                    if (!$refs->contains($resource)) {
                        $refs->add($resource);
                    }
                } catch (\Exception $e) {
                };
            }
        }
        
        if ($build) {
            $this->build();
        }
        $this->hasChanged = true;
        return $this;
    }

    /**
     * Revokes a permission from a user or resource.
     *
     * {@inheritDoc}
     *
     * @param bool $build Should the view and change arrays be rebuild?
     *
     * @return $this|PermissionsInterface
     */
    public function revoke($resource, $permission = null, $build = true)
    {
        
        if (self::PERMISSION_NONE == $permission || !$this->isAssigned($resource)) {
            return $this;
        }
        
        if (self::PERMISSION_CHANGE == $permission) {
            return $this->grant($resource, self::PERMISSION_VIEW, $build);
        }
        
        return $this->grant($resource, self::PERMISSION_NONE, $build);
        
    }
    
    public function clear()
    {
        $this->view      = array();
        $this->change    = array();
        $this->assigned  = array();
        $this->resources = null;
        
        return $this;
    }
    
    public function inherit(PermissionsInterface $permissions, $build = true)
    {
        // Override permissions type temporarly to get the right permissions back
        // from resources which may be aware of the permissions type.
        // Maybe this must be controllable by an additional parameter, but for now
        // we make this default.
        $oldType = $this->type;
        $this->type = $permissions->getType();

        /* @var $permissions Permissions */
        $assigned  = $permissions->getAssigned();
        $resources = $permissions->getResources();
    
        /*
         * Grant resource references permissions.
         */
        foreach ($resources as $resource) {
            /* @var $resource PermissionsResourceInterface */
            $permission = $permissions->getFrom($resource);

            $this->grant($resource, $permission, false);
            unset($assigned[$resource->getPermissionsResourceId()]);
        }
        /*
         * Merge remaining user permissions (w/o resource references)
         */
        $this->assigned = array_merge($this->assigned, $assigned);
        if ($build) {
            $this->build();
        }
        $this->hasChanged= true;

        // restore orginial permissions type
        $this->type = $oldType;

        return $this;
    }

    /**
     * Builds the user id lists.
     *
     * This will make database queries faster and also the calls to {@link isGranted()} will be faster.
     *
     * @return self
     */
    public function build()
    {
        $view = $change = array();
        foreach ($this->assigned as $resourceId => $spec) {
            /* This is needed to convert old permissions to the new spec format
             * introduced in 0.18
             * TODO: Remove this line some versions later.
             */
            // @codeCoverageIgnoreStart
            if (isset($spec['permission'])) {
                $spec = array($spec['permission'] => $spec['users']);
                $this->assigned[$resourceId] = $spec;
            }
            // @codeCoverageIgnoreEnd

            foreach ($spec as $perm => $userIds) {
                if (self::PERMISSION_ALL == $perm || self::PERMISSION_CHANGE == $perm) {
                    $change = array_merge($change, $userIds);
                }
                $view = array_merge($view, $userIds);
            }
        }
        
        $this->change = array_unique($change);
        $this->view   = array_unique($view);
        return $this;
    }
    
    private function checkIsGranted($userId, $permission)
    {
        if (!$userId) { return false; }

        if (self::PERMISSION_NONE == $permission) {
            return !in_array($userId, $this->view);
        }
        
        if (self::PERMISSION_ALL == $permission || self::PERMISSION_CHANGE == $permission) {
            return in_array($userId, $this->change);
        }

        // Now there's only PERMISSION_VIEW left to check.
        return in_array($userId, $this->view);
    }

    public function isGranted($userOrId, $permission)
    {
        if ($userOrId instanceOf UserInterface) {
            $id = $userOrId->getId();
            $role = $userOrId->getRole();
        } else {
            $id = (string) $userOrId;
            $role = null;
        }

        $this->checkPermission($permission);

        return $this->checkIsGranted($id, $permission)
               || ($this->isAssigned($role) && $this->checkIsGranted($role, $permission))
               || ($this->isAssigned('all') && $this->checkIsGranted('all', $permission));
    }
    
    public function isAssigned($resource)
    {
        $resourceId = $this->getResourceId($resource);
        return isset($this->assigned[$resourceId]);
    }
    
    public function hasChanged()
    {
        return $this->hasChanged;
    }

    /**
     * Gets the assigned specification.
     *
     * This is only needed when inheriting this permissions object into another.
     *
     * @return array
     */
    public function getAssigned()
    {
        return $this->assigned;
    }

    /**
     * Gets the resource collection.
     *
     * This is only needed when inheriting.
     *
     * @internal
     *      The PrePersist hook is needed, because eventually
     *      this method is called during the onFlush event by
     *      an UpdateFilePermission-Listener. Generating
     *      an ArrayCollection in this state leads to a
     *      fatal error deep in Doctrine.
     *
     *      This PrePersist hook assures, that there is
     *      a prefilled ArrayCollection during the
     *      changeset computation.
     *
     * @ODM\PrePersist
     *
     * @return Collection
     */
    public function getResources()
    {
        if (!$this->resources) {
            $this->resources = new ArrayCollection();
        }
        return $this->resources;
    }


    public function getFrom($resource)
    {
        $resourceId = $this->getResourceId($resource);

        if (!isset($this->assigned[$resourceId])) {
            return self::PERMISSION_NONE;
        }

        $spec = $this->assigned[$resourceId];

        return 1 == count($spec) ? key($spec) : null;
    }
    

    /**
     * Gets/Generates the resource id.
     *
     * @param string|UserInterface|PermissionsResourceInterface $resource
     *
     * @return string
     */
    protected function getResourceId($resource)
    {
        if ($resource instanceof PermissionsResourceInterface) {
            return $resource->getPermissionsResourceId();
        }
        
        if ($resource instanceof UserInterface) {
            return 'user:' . $resource->getId();
        }
        
        return 'user:' . $resource;
    }

    /**
     * Checks a valid permission.
     *
     * @param string $permission
     *
     * @throws \InvalidArgumentException
     */
    protected function checkPermission($permission)
    {
        $perms = array(
            self::PERMISSION_ALL,
            self::PERMISSION_CHANGE,
            self::PERMISSION_NONE,
            self::PERMISSION_VIEW,
        );
        if (!in_array($permission, $perms)) {
            throw new \InvalidArgumentException(
                'Invalid permission. Must be one of ' . implode(', ', $perms)
            );
        }
    }
}
