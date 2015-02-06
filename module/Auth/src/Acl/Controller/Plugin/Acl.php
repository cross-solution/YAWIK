<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Acl.php */ 
namespace Acl\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Permissions\Acl\AclInterface;
use Auth\Entity\UserInterface;
use Auth\Exception\UnauthorizedAccessException;
use Core\Entity\FileInterface;
use Auth\Exception\UnauthorizedImageAccessException;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Acl
 * @package Acl\Controller\Plugin
 */
class Acl extends AbstractPlugin
{
    protected $acl;
    protected $user;

    /**
     * @param AclInterface $acl
     * @param UserInterface $user
     */
    public function __construct(AclInterface $acl, UserInterface $user = null)
    {
        $this->setAcl($acl);
        if (null !== $user) {
            $this->setUser($user);
        }
    }

    /**
     * @return AclInterface
     */
    public function getAcl ()
    {
        return $this->acl;
    }

    /**
     * @param AclInterface $acl
     * @return $this
     */
    public function setAcl (AclInterface $acl)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * @return \Auth\Entity\User
     */
    public function getUser ()
    {
        if (!$this->user) {
            $this->user = new \Auth\Entity\User();
            $this->user->setRole('guest');
        }
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser (UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param $role
     * @param null $inherit
     * @return bool
     */
    public function isRole($role, $inherit=null)
    {
        if ($role instanceOf RoleInterface) {
            $role = $role->getRoleId();
        }

        $isRole = $this->getUser()->getRole() == $role;
        
        return null === $inherit
               ? $isRole
               : $isRole || $this->getAcl()->inheritRole($role, $inherit);
    }

    /**
     * @param $resource
     * @param null $privilege
     * @return bool
     */
    public function test($resource, $privilege=null)
    {
        return $this->getAcl()->isAllowed($this->getUser(), $resource, $privilege);
    }

    /**
     * @param $resource
     * @param null $privilege
     * @throws \Auth\Exception\UnauthorizedImageAccessException
     * @throws \Auth\Exception\UnauthorizedAccessException
     */
    public function check($resource, $privilege=null)
    {
        if (!$this->test($resource, $privilege)) {
            
            $msg = null === $privilege
                 ? sprintf('You are not allowed to access resource "%s"',
                           is_object($resource) ? $resource->getResourceId() : $resource
                   )
                 : sprintf('You are not allowed to execute operation "%s" on resource "%s"',
                           $privilege, is_object($resource) ? $resource->getResourceId() : $resource
                   );
            
            if ($resource instanceOf FileInterface && 0 == strpos($resource->type, 'image/')) {
                throw new UnauthorizedImageAccessException(str_replace('resource', 'image', $msg));
            }
            throw new UnauthorizedAccessException($msg);
        }
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @param string $mode
     * @return $this|bool
     */
    public function __invoke($resource=null, $privilege=null, $mode='check')
    {
        if (null === $resource) {
            return $this;
        }
        
        if ('test' == $mode) {
            return $this->test($resource, $privilege);
        }
        
        $this->check($resource, $privilege);
    }

}

