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

use Core\Entity\FileMetadataInterface;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;

/**
* ensures that attachments can be viewed only by persons who have access to the application.
* eg. a recruiter may only see an attached file of an application, if he owns the application.
*/

class FileAccessAssertion implements AssertionInterface
{
    /* (non-PHPdoc)
     * @see \Laminas\Permissions\Acl\Assertion\AssertionInterface::assert()
     */
    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        if (!$role instanceof UserInterface || !$resource instanceof FileMetadataInterface) {
            return false;
        }
        
        $privilege = $privilege ?: PermissionsInterface::PERMISSION_VIEW;
        return $resource->getPermissions()->isGranted($role, $privilege)
            || $resource->getPermissions()->isGranted('all', $privilege)
            || UserInterface::ROLE_ADMIN == $role->getRole();
    }
}
