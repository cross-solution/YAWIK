<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Form\Element;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Organizations\Form\Element\Employee;

/**
 * Tests for Employee element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Form
 * @group Organizations.Form.Element
 */
class EmployeePermissionsTest extends TestCase
{

    /**
     * Does the element extends the correct base class?
     */
    public function testExtendsBaseClass()
    {
        $target = new Employee();

        $this->assertInstanceOf('\Zend\Form\Element', $target);
    }

    /**
     * Does getValue returns template placeholder, if value is not UserInterface?
     * Does getValue returns users' id, if value is indeed an user?
     */
    public function testGetValue()
    {
        $target = new Employee();

        $this->assertEquals('__userId__', $target->getValue());

        $user = new User();
        $user->setId('test1234');
        $target->setValue($user);

        $this->assertEquals('test1234', $target->getValue());
    }

    /**
     * Does getUser returns null, if value is not set or not UserInterface?
     * Does getUser reurns the User, if one is set?
     */
    public function testGetUser()
    {
        $target = new Employee();
        $user = new User();

        $user->setId('test1234');

        $this->assertNull($target->getUser());

        $target->setValue($user);
        $this->assertSame($user, $target->getUser());

        $target->setValue('test1234');

        $this->assertNull($target->getUser());
    }
}
