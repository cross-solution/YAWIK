<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Acl.php */ 
namespace Acl\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Permissions\Acl\AclInterface;
use Auth\Entity\UserInterface;
use Auth\Exception\UnauthorizedAccessException;
use Core\Entity\FileEntityInterface;
use Auth\Exception\UnauthorizedImageAccessException;

class Acl extends AbstractPlugin
{
    protected $acl;
    protected $user;
    
    public function __construct(AclInterface $acl, UserInterface $user = null)
    {
        $this->setAcl($acl);
        if (null !== $user) {
            $this->setUser($user);
        }
    }
	/**
     * @return the $acl
     */
    public function getAcl ()
    {
        return $this->acl;
    }

	/**
     * @param field_type $acl
     */
    public function setAcl (AclInterface $acl)
    {
        $this->acl = $acl;
        return $this;
    }

	/**
     * @return the $user
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
     * @param field_type $user
     */
    public function setUser (UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function test($resource, $privilege=null)
    {
        return $this->getAcl()->isAllowed($this->getUser(), $resource, $privilege);
    }
    
    public function check($resource, $privilege=null)
    {
        if (!$this->test($resource, $privilege)) {
            if ($resource instanceOf FileEntityInterface && 0 == strpos($resource->type, 'image/')) {
                throw new UnauthorizedImageAccessException('User access denied');
            }
            throw new UnauthorizedAccessException('User access denied');
        }
    }
    
    public function __invoke($resource, $privilege=null, $mode='check')
    {
        if ('test' == $mode) {
            return $this->test($resource, $privilege);
        }
        $this->check($resource, $privilege);
    }

}

