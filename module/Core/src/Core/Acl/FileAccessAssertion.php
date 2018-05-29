<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileAccessAssertion.php */
namespace Core\Acl;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Core\Entity\FileInterface;
use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;

/**
* ensures that attachments can be viewed only by persons who have access to the application.
* eg. a recruiter may only see an attached file of an application, if he owns the application.
*/

class FileAccessAssertion implements AssertionInterface
{
    /* (non-PHPdoc)
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        if (!$role instanceof UserInterface || !$resource instanceof FileInterface) {
            return false;
        }
        
        $privilege = $privilege ?: PermissionsInterface::PERMISSION_VIEW;
        return $resource->getPermissions()->isGranted($role, $privilege)
            || $resource->getPermissions()->isGranted('all', $privilege)
            || UserInterface::ROLE_ADMIN == $role->getRole();
    }
}
