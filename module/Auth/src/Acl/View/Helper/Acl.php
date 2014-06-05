<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Acl.php */ 
namespace Acl\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Acl\Controller\Plugin\Acl as AclPlugin;
use Zend\Permissions\Acl\Resource\ResourceInterface;


class Acl extends AbstractHelper
{
    
    protected $aclPlugin;

    public function __construct(AclPlugin $aclPlugin)
    {
        $this->setAclPlugin($aclPlugin);
    }
    
    /**
     * @return the $acl
     */
    public function getAclPlugin ()
    {
        return $this->aclPlugin;
    }

    /**
     * @param field_type $acl
     */
    public function setAclPlugin (AclPlugin $aclPlugin)
    {
        $this->aclPlugin = $aclPlugin;
        return $this;
    }

    public function __invoke($resource=null, $privilege=null, $mode='test')
    {
        return $this->getAclPlugin()->__invoke($resource, $privilege, $mode);
    }
}

