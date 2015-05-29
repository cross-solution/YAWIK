<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Entity;

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
class EmployeeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Class under Test
     *
     * @var Employee
     */
    private $target;

    public function setup()
    {
        if ('testCreateInstancesViaConstructor' == $this->getName(false)) {
            return;
        }
        $user = $this->getMock('\Auth\Entity\User');
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
        $user = $this->getMock('\Auth\Entity\User');
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


            if ($permissions instanceOf EmployeePermissions) {
                $this->assertSame($permissions, $target->getPermissions());
            } else if (is_int($permissions)) {
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

        $object = $target->$setter($value);
        $this->assertSame($target->$getter(), $value);
        $this->assertSame($target, $object);
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
     * Provides datasets for testSettingValuesViaSetterMethods.
     *
     * @return array
     */
    public function provideSetterTestValues()
    {
        return array(
            array('setUser', 'getUser', new User()),
            array('setPermissions', 'getPermissions', new EmployeePermissions()),
            array('setIsPending', 'isPending', true),
            array('setIsPending', 'isPending', false)
        );
    }
    
}