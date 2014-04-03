<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Permissions.php */ 
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;
use Auth\Entity\GroupInterface;
use Core\Entity\Collection\ArrayCollection;

/**
 * 
 * @ODM\EmbeddedDocument
 */
class Permissions implements PermissionsInterface
{
    
    /**
     * Permissions container
     * Array of Format <user_id> => <permission>
     * @var array
     * @ODM\Collection
     */
    protected $view = array();

    /**
     * 
     * @var array
     * @ODM\Collection
     */
    protected $change = array();
    
    /**
     * @var array
     * @ODM\Hash
     */
    protected $assigned = array();
    
    /**
     * 
     * @var unknown
     * @ODM\ReferenceMany(discriminatorField="_resource")
     */
    protected $resources;
    
    
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
    
    public function __call($method, $params)
    {
        if (1 < count($params)) {
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
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsInterface::grantTo()
     */
    public function grant ($resource, $permission, $build = true)
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
        true === $permission || $this->checkPermission($permission);
        
        list ($resourceId, $userIds) = $this->getResourceId($resource, true);
        
        if (true === $permission) {
            $permission = isset($this->assigned[$resourceId])
                        ? $this->assigned[$resourceId]['permission']
                        : self::PERMISSION_NONE;
        }
        
        if (self::PERMISSION_NONE == $permission) {
            if ($resource instanceOf PermissionsResourceInterface) {
                $refs = $this->getResources();
                if ($refs->contains($resource)) {
                    $refs->removeElement($resource);
                }
            }
            unset($this->assigned[$resourceId]);
        } else {
            $this->assigned[$resourceId] = array(
                'users'      => $userIds,
                'permission' => $permission,
            );
            if ($resource instanceOf PermissionsResourceInterface) {
                $refs = $this->getResources();
                if (!$refs->contains($resource)) {
                    $refs->add($resource);
                }
            }
        }
        
        if ($build) {
            $this->build();
        }
        return $this;
    }
    
    public function revoke ($resource, $permission)
    {
        
        if (self::PERMISSION_NONE == $permission || !$this->isAssigned($resource)) {
            return $this;
        }
        
        if (self::PERMISSION_CHANGE == $permission) {
            return $this->grant($resource, self::PERMISSION_VIEW);
        }
        
        return $this->grant($resource, self::PERMISSION_NONE);
        
    }
    
    public function inherit(PermissionsInterface $permissions, $build=true)
    {
        $assigned  = $permissions->getAssigned();
        $resources = $permissions->getResources();
    
        /*
         * Grant resource references permissions.
         */
        foreach ($resources as $resource) {
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
        return $this;
    }
    
    public function build()
    {
        $view = $change = array();
        foreach ($this->assigned as $spec) {
            if (self::PERMISSION_ALL == $spec['permission'] || self::PERMISSION_CHANGE == $spec['permission']) {
                $change = array_merge($change, $spec['users']);
            }
            $view = array_merge($view, $spec['users']);
        }
        
        $this->change = array_unique($change);
        $this->view   = array_unique($view);
        return $this;
    }
    
    /** 
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsInterface::isGranted()
     */
    public function isGranted ($userOrId, $permission)
    {
        $userId = $this->getUserId($userOrId);
        $this->checkPermission($permission);
        
        if (self::PERMISSION_NONE == $permission) {
            return !in_array($userId, $this->view);
        }
        
        if (self::PERMISSION_ALL == $permission || self::PERMISSION_CHANGE == $permission) {
            return in_array($userId, $this->change);
        }
        
        if (self::PERMISSION_VIEW == $permission) {
            return in_array($userId, $this->view);
        }
        
    }
    
    public function isAssigned($resource)
    {
        $resourceId = $this->getResourceId($resource);
        return isset($this->assigned[$resourceId]);
    }
    
    public function getAssigned()
    {
        return $this->assigned;
    }
    
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
        return isset($this->assigned[$resourceId])
               ? $this->assigned[$resourceId]['permission']
               : self::PERMISSION_NONE;
    }
    
    protected function getUserId($userOrId)
    {
        return $userOrId instanceOf UserInterface
               ? $userOrId->getId()
               : (string) $userOrId;
    }
    
    protected function getResourceId($resource, $includeUsers = false)
    {
        if ($resource instanceOf PermissionsResourceInterface) {
            return $includeUsers
                   ? array($resource->getPermissionsResourceId(), $resource->getPermissionsUserIds())
                   : $resource->getPermissionsResourceId();
        }
        
        if ($resource instanceOf UserInterface) {
            return $includeUsers
                   ? array('user:' . $resource->getId(), array($resource->getId()))
                   : $resource->getId();
        }
        
        return $includeUsers
               ? array('user:' . $resource, array($resource))
               : $resource;
    }
    
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

