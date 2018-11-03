<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** JobAccessAssertion.php */
namespace Jobs\Acl;

use Acl\Assertion\AbstractEventManagerAwareAssertion;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Auth\Entity\UserInterface;

/**
 * This assertion checks permissions to create a job.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
class CreateAssertion extends AbstractEventManagerAwareAssertion
{
    protected $identifiers = array(
         'Jobs/Acl/Assertions',
         'Jobs/Acl/Assertion/Create',
    );

    protected function preAssert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        if (!$role instanceof UserInterface || 'new' != $privilege) {
            return false;
        }

        return null;
    }
}
