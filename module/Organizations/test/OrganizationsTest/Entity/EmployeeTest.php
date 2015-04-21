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
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Entity
 */
class EmployeeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Does the entity implement the correct interface?
     *
     */
    public function testEmployeeImplementsInterface()
    {
        $target = new Employee();

        $this->assertInstanceOf('\Organizations\Entity\EmployeeInterface', $target);
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
        $target = new Employee();

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
        $target = new Employee();
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
            array('setPending', 'isPending', true),
            array('setPending', 'isPending', false)
        );
    }
    
}