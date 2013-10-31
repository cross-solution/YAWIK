<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Acl.php */ 
namespace Acl\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Permissions\Acl\AclInterface;
use Auth\Entity\UserInterface;

class Acl extends AbstractHelper
{
    
    protected $acl;
    protected $user;

    
    public function __construct(AclInterface $acl, UserInterface $user)
    {
        $this->setAcl($acl);
        $this->setUser($user);
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
    
    public function __invoke($resource, $privilege=null)
    {
        return $this->test($resource, $privilege);
    }
}

