<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Acl.php */
namespace Acl\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Acl\Controller\Plugin\Acl as AclPlugin;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

class Acl extends AbstractHelper
{
    /**
     * @var AclPlugin
     */
    protected $aclPlugin;

    /**
     * @param AclPlugin $aclPlugin
     */
    public function __construct(AclPlugin $aclPlugin)
    {
        $this->setAclPlugin($aclPlugin);
    }
    
    /**
     * @return AclPlugin $acl
     */
    public function getAclPlugin()
    {
        return $this->aclPlugin;
    }

    /**
     * @param AclPlugin $aclPlugin
     * @return $this
     */
    public function setAclPlugin(AclPlugin $aclPlugin)
    {
        $this->aclPlugin = $aclPlugin;
        return $this;
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @param string $mode
     * @return $this
     */
    public function __invoke($resource = null, $privilege = null, $mode = 'test')
    {
        return $this->getAclPlugin()->__invoke($resource, $privilege, $mode);
    }
}
