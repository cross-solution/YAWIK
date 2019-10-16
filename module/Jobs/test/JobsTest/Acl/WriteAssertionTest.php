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

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Jobs\Acl\WriteAssertion;
use Core\Entity\Permissions;
use Organizations\Entity\EmployeePermissionsInterface;
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
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Jobs
 * @group Jobs.Acl
 */
class WriteAssertionTest extends TestCase
{
    /**
     * @var WriteAssertion
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new WriteAssertion();
    }

    /**
     * @coversNothing
     */
    public function testExtendsBaseClass()
    {
        $this->assertInstanceOf('Zend\Permissions\Acl\Assertion\AssertionInterface', $this->target);
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

        foreach (array($role, $user) as $testObj) {
            // non user is always false?
            foreach ($privileges as $p) {
                $this->assertFalse((bool)$target->assert($acl, $testObj, null, $p));
            }

            // user and wrong privilege is false?
            foreach ($privileges as $p) {
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
        $this->assertFalse($assertion->checkOrganizationPermissions($user, $job));
    }

    /**
     * @dataProvider assertParametersWithoutOrganization
     */
    public function testAssertWithoutOrganisation($input, $expected)
    {
        $method="assert".($expected?"True":"False");

        $this->$method(
                $this->target->assert(
                    $input[0], // acl
                    $input[1], // role
                    $input[2], // resource
                    $input[3]  // privilege
                )
            );
    }

    public function assertParametersWithoutOrganization()
    {
        $userId = 1234;
        $user = new User();
        $user->setId($userId);

        $permissionsMock = $this->getMockBuilder('Core\Entity\Permissions')
            ->setMethods(['isGranted'])
            ->getMock();
        $permissionsMock
            ->expects($this->exactly(1))
            ->method('isGranted')
            ->with($userId)
            ->will($this->onConsecutiveCalls(true));

        $jobMock = $this->getMockBuilder('Jobs\Entity\Job')
            ->setMethods(['getPermissions'])
            ->getMock();
        $jobMock
            ->expects($this->exactly(1))
            ->method('getPermissions')
            ->willReturn($permissionsMock);


        return [
            [[new Acl(), null, null, null] , false ],
            [[new Acl(), null, null, Permissions::PERMISSION_CHANGE] , false ],
            [[new Acl(), $user, $jobMock, 'edit'] , true ],
        ];
    }

    public function testAssertUserIsOrganizationAdmin()
    {
        $userId = 1234;
        $user = new User();
        $user->setId($userId);

        $organization = new Organization();
        $organization->setUser($user);

        $permissionsMock = $this->getMockBuilder('Core\Entity\Permissions')
            ->setMethods(['isGranted'])
            ->getMock();
        $permissionsMock->expects($this->once())->method('isGranted')->willReturn(false);
        $jobMock = $this->getMockBuilder('Jobs\Entity\Job')
            ->setMethods(['getPermissions', 'getOrganization'])
            ->getMock();

        $jobMock->expects($this->once())->method('getPermissions')->willReturn($permissionsMock);
        $jobMock->expects($this->once())->method('getOrganization')->willReturn($organization);

        $this->assertTrue(
            $this->target->assert(
                new Acl(), // acl
                $user,     // role
                $jobMock,  // resource
                'edit'     // privilege
            )
        );
    }

    public function testAssertUserIsOwnerOfTheParentOrganization()
    {
        $userId = 1234;
        $user = new User();
        $user->setId($userId);

        $organization = new Organization();

        $parentOrganization = new Organization();
        $parentOrganization->setUser($user);

        $organization->setParent($parentOrganization);

        $permissionsMock = $this->getMockBuilder('Core\Entity\Permissions')
            ->setMethods(['isGranted'])
            ->getMock();
        $permissionsMock->expects($this->once())->method('isGranted')->willReturn(false);
        $jobMock = $this->getMockBuilder('Jobs\Entity\Job')
            ->setMethods(['getPermissions', 'getOrganization'])
            ->getMock();

        $jobMock->expects($this->once())->method('getPermissions')->willReturn($permissionsMock);
        $jobMock->expects($this->once())->method('getOrganization')->willReturn($organization);

        $this->assertTrue(
            $this->target->assert(
                new Acl(), // acl
                $user,     // role
                $jobMock,  // resource
                'edit'     // privilege
            )
        );
    }

    public function testUserIsEmployeeWithJobsChangePermissions()
    {
        $userId = 1234;
        $user = new User();
        $user->setId($userId);

        $organization = new Organization();

        $permissionsMock = $this->getMockBuilder('Core\Entity\Permissions')
            ->setMethods(['isGranted', 'isAllowed'])
            ->getMock();
        $permissionsMock->expects($this->once())->method('isGranted')->willReturn(false);
        $permissionsMock->expects($this->once())->method('isAllowed')->with(EmployeePermissionsInterface::JOBS_CHANGE)->willReturn(true);

        $employeeMock = $this->getMockBuilder('Organizations\Entity\Employee')
            ->setMethods(['getPermissions', 'getUser'])
            ->getMock();
        $employeeMock->expects($this->once())->method('getPermissions')->willReturn($permissionsMock);
        $employeeMock->expects($this->once())->method('getUser')->willReturn($user);

        $employees = new ArrayCollection();
        $employees->add($employeeMock);
        $organization->setEmployees($employees);

        $jobMock = $this->getMockBuilder('Jobs\Entity\Job')
            ->setMethods(['getPermissions', 'getOrganization'])
            ->getMock();

        $jobMock->expects($this->once())->method('getPermissions')->willReturn($permissionsMock);
        $jobMock->expects($this->once())->method('getOrganization')->willReturn($organization);

        $this->assertTrue(
            $this->target->assert(
                new Acl(), // acl
                $user,     // role
                $jobMock,  // resource
                'edit'     // privilege
            )
        );
    }
}

class CreateWriteAssertionMock extends WriteAssertion
{
    public function assert(
        Acl $acl,
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
