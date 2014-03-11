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
    
    
    public function __call($method, $params)
    {
        if (1 < count($params)) {
            throw new \InvalidArgumentException('Missing user entity or user id.');
        }
        
        if (preg_match('~^is(View|Change|None|All)Granted$~', $method, $match)) {
            $permission = constant('self::PERMISSION_' . strtoupper($match[1]));
            return $this->isGranted($params[0], $permission);
        }
        
        if (preg_match('~^grant(View|Change|None|All)To$~', $method, $match)) {
            $permission = constant('self::PERMISSION_' . strtoupper($match[1]));
            return $this->grantTo($params[0], $permission);
        }
        
        if (preg_match('~^revoke(View|Change|None|All)From$~', $method, $match)) {
            $permission = constant('self::PERMISSION_' . strtoupper($match[1]));
            return $this->revokeFrom($params[0], $permission);
        }
        
        throw new \BadMethodCallException('Unknown method "' . $method . '"');
    }
    /** 
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsInterface::getFrom()
     */
    public function getFrom ($userOrId)
    {
        $userId = $this->getUserId($userOrId);
        
        if ($this->isGranted($userId, self::PERMISSION_CHANGE)) {
            return self::PERMISSION_CHANGE;
        }
        
        if ($this->isGranted($userId, self::PERMISSION_VIEW)) {
            return self::PERMISSION_VIEW;
        }
        
        return self::PERMISSION_NONE;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsInterface::grantTo()
     */
    public function grantTo ($userOrId, $permission)
    {
        $userId = $this->getUserId($userOrId);
        $this->checkPermission($permission);
        
        if (self::PERMISSION_NONE == $permission) {
            return $this->revokeFrom($userOrId, self::PERMISSION_ALL);
        }
        
        if (self::PERMISSION_CHANGE == $permission || self::PERMISSION_ALL == $permission) {
            if (!in_array($userId, $this->view)) {
                $this->view[] = $userId;
            }
            if (!in_array($userId, $this->change)) {
                $this->change[] = $userId;
            }
            return $this;
        }
        
        if (self::PERMISSION_VIEW == $permission) {
            $this->revokeFrom($userId, self::PERMISSION_CHANGE);
            if (!in_array($userId, $this->view)) {
                $this->view[] = $userId;
            }
        }
        
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
            return !in_array($userId, $this->view) && !in_array($userId, $this->change);
        }
        
        if (self::PERMISSION_ALL == $permission || self::PERMISSION_CHANGE == $permission) {
            return in_array($userId, $this->view) && in_array($userId, $this->change);
        }
        
        if (self::PERMISSION_VIEW == $permission) {
            return in_array($userId, $this->view) && !in_array($userId, $this->change);
        }
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsInterface::revokeFrom()
     */
    public function revokeFrom ($userOrId, $permission)
    {
        $userId = $this->getUserId($userOrId);
        $this->checkPermission($permission);
        
        if (self::PERMISSION_NONE == $permission) {
            return $this;
        }
        
        $filter = function($item) use ($userId) {
            return $item !== $userId;
        };
        
        if (self::PERMISSION_ALL == $permission || self::PERMISSION_VIEW == $permission) {
            $this->view = array_filter($this->view, $filter);
        }
        
        if (self::PERMISSION_ALL == $permission || self::PERMISSION_CHANGE == $permission) {
            $this->change = array_filter($this->change, $filter);
        }
        return $this;
    }

    protected function getUserId($userOrId)
    {
        return $userOrId instanceOf UserInterface
               ? $userOrId->getId()
               : (string) $userOrId;
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

