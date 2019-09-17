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

use PHPUnit\Framework\TestCase;

use Organizations\Form\EmployeesFieldset;

/**
 * Test for EmployeesFieldset
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizatios.Form
 */
class EmployeesFieldsetTest extends TestCase
{

    /**
     * Does the fieldset extends Zends' Fielset?
     * Does the fieldset implements ViewPartialProviderInterface?
     */
    public function testBaseClassAndImplementedInterfaces()
    {
        $target = new EmployeesFieldset();

        $this->assertInstanceOf('\Zend\Form\Fieldset', $target);
        $this->assertInstanceOf('\Core\Form\ViewPartialProviderInterface', $target);
    }

    /**
     * Is the default view partial correct?
     * Does setting and getting view partial work?
     */
    public function testSetAndGetViewPartial()
    {
        $target = new EmployeesFieldset();

        $this->assertEquals('organizations/form/employees-fieldset', $target->getViewPartial());

        $target->setViewPartial('test1234');
        $this->assertEquals('test1234', $target->getViewPartial());
    }

    /**
     * Is the name set?
     * Is the add method called with the expected arguments?
     */
    public function testInit()
    {
        $expectAdd1 = array(
            'name' => 'inviteemployee',
            'type' => 'Organizations/InviteEmployeeBar',
            'options' => array(
                'description' => 'Invite an employee via email address.',
            ),

        );

        $expectAdd2 = array(

            'type' => 'Collection',
            'name' => 'employees',
            'options' => array(
                'count' => 0,
                'should_create_template' => true,
                'use_labeled_items' => false,
                'allow_add' => true,
                'allow_remove' => true,
                'renderFieldset' => true,
                'target_element' => array(
                    'type' => 'Organizations/EmployeeFieldset'
                )
            ),
        );

        $target = $this->getMockBuilder('\Organizations\Form\EmployeesFieldset')
                ->disableOriginalConstructor()
                ->setMethods(array('add'))
                ->getMock();

        $target->expects($this->exactly(2))
               ->method('add')
               ->withConsecutive(
                   array($expectAdd1),
                   array($expectAdd2)
               );

        $target->init();
        $this->assertEquals('employees', $target->getName());
    }
}
