<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Organizations\Entity\Employee;
use Organizations\Entity\EmployeePermissions;

/**
 * Test the employee entity.
 *
 * @covers \Organizations\Entity\Employee
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class EmployeeTest extends TestCase
{

    /**
     * Class under Test
     *
     * @var Employee
     */
    private $target;

    protected function setUp(): void
    {
        if ('testCreateInstancesViaConstructor' == $this->getName(false)) {
            return;
        }
        $user = $this->getMockBuilder('\Auth\Entity\User')->getMock();
        $this->target = new Employee($user);
    }
    /**
     * Does the entity implement the correct interface?
     *
     */
    public function testEmployeeImplementsInterface()
    {
        $this->assertInstanceOf('\Organizations\Entity\EmployeeInterface', $this->target);
    }

    public function provideConstructorPermissions()
    {
        $user = $this->getMockBuilder('\Auth\Entity\User')->getMock();
        return array(
            array(null, null),
            array(null, EmployeePermissions::APPLICATIONS_VIEW),
            array($user, null),
            array($user, EmployeePermissions::ALL),
            array($user, new EmployeePermissions(EmployeePermissions::JOBS_CHANGE)),
        );
    }

    /**
     * @dataProvider provideConstructorPermissions
     * @covers \Organizations\Entity\Employee::__construct
     *
     * @param null|\Auth\Entity\UserInterface $user
     * @param null|int|EmployeePermissions $permissions
     */
    public function testCreateInstancesViaConstructor($user, $permissions)
    {
        $target = new Employee($user, $permissions);

        if (null === $user) {
            $this->assertAttributeEmpty('user', $target);
            $this->assertAttributeEmpty('permissions', $target);
        } else {
            $this->assertSame($user, $target->getUser());


            if ($permissions instanceof EmployeePermissions) {
                $this->assertSame($permissions, $target->getPermissions());
            } elseif (is_int($permissions)) {
                $this->assertEquals($permissions, $target->getPermissions()->getPermissions());
            } else {
                $this->assertAttributeEmpty('permissions', $target);
            }
        }
    }

    /**
     * Do setter and getter methods work correctly?
     *
     * @param string $setter Setter method name
     * @param string $getter getter method name
     * @param mixed $value Value to set and test the getter method with.
     *
     * @dataProvider provideSetterTestValues
     */
    public function testSettingValuesViaSetterMethods($setter, $getter, $value)
    {
        $target = $this->target;

        if (is_array($value)) {
            $setValue = $value[0];
            $getValue = $value[1];
        } else {
            $setValue = $getValue = $value;
        }

        if (null !== $setter) {
            $object = $target->$setter($setValue);
            $this->assertSame($target, $object, 'Fluent interface broken!');
        }

        $this->assertSame($target->$getter(), $getValue);
    }

    /**
     * Does getPermissions creates a new EmployeePermissions object?
     *
     */
    public function testGetPermissionsCreateNewObjectIfNotSet()
    {
        $target = $this->target;
        $perm   = $target->getPermissions();

        $this->assertInstanceOf('\Organizations\Entity\EmployeePermissionsInterface', $perm);
    }

    /**
     * @testdox Implements \Organizations\Entity\EmployeeInterface
     * @dataProvider provideStatusCheckData
     */
    public function testConvinientStatusCheckMethods($initialStatus, $expectedResults, $strict=null)
    {
        $this->target->setStatus($initialStatus);

        $this->assertEquals($expectedResults[0], $this->target->isAssigned(), 'isAssigned() fails!');
        $this->assertEquals($expectedResults[1], $this->target->isPending(), 'isPending fails!');
        $this->assertEquals($expectedResults[2], $this->target->isRejected(), 'isRejected fails!');
        $this->assertEquals($expectedResults[3], $this->target->isUnassigned(), 'isUnassigned fails!');
        $this->assertEquals($expectedResults[4], $this->target->isUnassigned(true), 'isUnassigned strict mode fails!');
    }

    /**
     * Provides datasets for testSettingValuesViaSetterMethods.
     *
     * @return array
     */
    public function provideSetterTestValues()
    {
        return array(
            array('setUser', 'getUser', new User()),
            array('setPermissions', 'getPermissions', new EmployeePermissions()),
            array('setStatus', 'getStatus', Employee::STATUS_PENDING),
            array('setStatus', 'getStatus', Employee::STATUS_ASSIGNED),
            array('setStatus', 'getStatus', Employee::STATUS_REJECTED),
            array('setStatus', 'getStatus', Employee::STATUS_UNASSIGNED),
            array('setStatus', 'getStatus', array('Invalid Status', Employee::STATUS_ASSIGNED)),
            array(null, 'getStatus', Employee::STATUS_ASSIGNED),
        );
    }

    public function provideStatusCheckData()
    {
        return array(
            array(Employee::STATUS_ASSIGNED, array(true, false, false, false, false)),
            array(Employee::STATUS_PENDING, array(false, true, false, true, false)),
            array(Employee::STATUS_UNASSIGNED, array(false, false, false, true, true)),
            array(Employee::STATUS_REJECTED, array(false, false, true, true, false)),
        );
    }

    /**
     * @testdox Implements \Organizations\Entity\EmployeeInterface
     * @dataProvider provideEmployeeRoles
     */
    public function testSetGetRole($role)
    {
        $this->target->setRole($role);
        $this->assertEquals($role, $this->target->getRole());
    }


    public function provideEmployeeRoles()
    {
        return array(
            array(Employee::ROLE_DEPARTMENT_MANAGER),
            array(Employee::ROLE_MANAGEMENT),
            array(Employee::ROLE_RECRUITER),
        );
    }
}
