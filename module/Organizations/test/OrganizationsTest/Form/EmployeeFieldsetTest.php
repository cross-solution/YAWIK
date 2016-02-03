<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Form;

use Organizations\Entity\EmployeeInterface;
use Organizations\Form\EmployeeFieldset;
use Organizations\Entity\EmployeePermissionsInterface as Perms;

/**
 * Test for EmployeeFieldset
 *
 * @covers \Organizations\Form\EmployeeFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizatios.Form
 */
class EmployeeFieldsetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Does the fieldset extends Zends' Fielset?
     * Does the fieldset implements ViewPartialProviderInterface?
     */
    public function testBaseClassAndImplementedInterfaces()
    {
        $target = new EmployeeFieldset();

        $this->assertInstanceOf('\Zend\Form\Fieldset', $target);
        $this->assertInstanceOf('\Core\Form\ViewPartialProviderInterface', $target);
    }

    /**
     * Is the default view partial correct?
     * Does setting and getting view partial work?
     */
    public function testSetAndGetViewPartial()
    {
        $target = new EmployeeFieldset();

        $this->assertEquals('organizations/form/employee-fieldset', $target->getViewPartial());

        $target->setViewPartial('test1234');
        $this->assertEquals('test1234', $target->getViewPartial());
    }

    /**
     * Is the add method called with the expected arguments?
     */
    public function testInit()
    {
        $expectAdd1 = array(
            'type' => 'Organizations/Employee',
            'name' => 'user',
        );

        $expectAdd2 = array(
            'type' => 'MultiCheckbox',
            'name' => 'permissions',
            'options' => array(
                'value_options' => array(
                    Perms::JOBS_VIEW => 'View Jobs',
                    Perms::JOBS_CHANGE => 'Edit Jobs',
                    Perms::JOBS_CREATE => 'Create Jobs',
                    Perms::APPLICATIONS_VIEW => 'View Applications',
                    Perms::APPLICATIONS_CHANGE => 'Edit Applications',
                ),
            ),
        );

        $expectAdd3 = array(
            'type' => 'hidden',
            'name' => 'status',
            'attributes' => array(
                'value' => EmployeeInterface::STATUS_PENDING,
            ),
        );

        $target = $this->getMockBuilder('\Organizations\Form\EmployeeFieldset')
                ->disableOriginalConstructor()
                ->setMethods(array('add'))
                ->getMock();

        $target->expects($this->exactly(3))
               ->method('add')
               ->withConsecutive(
                    array($expectAdd1),
                    array($expectAdd2),
                    array($expectAdd3)
               );

        $target->init();
    }
}