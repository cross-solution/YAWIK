<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Acl;

use Auth\Entity\User;
use Jobs\Acl\WriteAssertion;
use Organizations\Entity\OrganizationReference;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;
use Jobs\Entity;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;

/**
 * Test of Jobs\Acl\WriteAssertion
 *
 * @covers \Jobs\Acl\WriteAssertion
 * @author Sergei <sergei@thephpguys.com>
 * @group Jobs
 * @group Jobs.Acl
 */
class WriteAssertionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @coversNothing
     */
    public function testExtendsBaseClass()
    {
        $target = new WriteAssertion();

        $this->assertInstanceOf('Zend\Permissions\Acl\Assertion\AssertionInterface', $target);
    }

    public function _testPreAssertConditions()
    {
        $target = new CreateWriteAssertionMock();

        $acl = new Acl();
        $job = new \Jobs\Entity\Job();

        /** Role **/
        $role = new GenericRole('test');
        $user = new User();

        $privileges = array('new', 'test', 'edit');

        foreach(array($role, $user) as $testObj)
        {
            // non user is always false?
            foreach($privileges as $p)
            {
                $this->assertFalse((bool)$target->assert($acl, $testObj, null, $p));
            }

            // user and wrong privilege is false?
            foreach($privileges as $p)
            {
                // Casting null to false is safe here, 'edit' for pair user-privilege tested after
                $this->assertFalse((bool)$target->assert($acl, $testObj, $job, $p));
            }
        }

    }

    public function testOrganisationPermissions()
    {
        $assertion = new CreateWriteAssertionMock();

        $role = new GenericRole('test');
        $user = new User();
        $job = new \Jobs\Entity\Job();

        $obj1 = $obj2 = "test string not an object";

        /** Incorrect class params */
        /**
        $this->assertFalse( $assertion->checkOrganizationPermissions( $obj1, $obj2 ) );

        $this->assertFalse( $assertion->checkOrganizationPermissions( $role, $job ) );
        $this->assertFalse( $assertion->checkOrganizationPermissions( $user, $job ) );
         * **/


        $input1 = "Company ABC";
        $input2 = "Another Company";
        $job->setCompany($input1);

        $organization = new Organization();
        $organizationName = new OrganizationName();

        $organizationName->setName($input2);
        $organization->setOrganizationName($organizationName);

        $job->setOrganization($organization);

        //$user->setOrganization($organization);

        /** Organization without user **/
        $this->assertFalse( $assertion->checkOrganizationPermissions( $user, $job ) );
    }

}

class CreateWriteAssertionMock extends WriteAssertion
{
    public function assert(Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        return parent::preAssert($acl, $role, $resource, $privilege);
    }

    public function checkOrganizationPermissions($role, $resource)
    {
        return parent::checkOrganizationPermissions($role, $resource);
    }
}
