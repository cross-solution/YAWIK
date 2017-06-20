<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Factory\Form;

use Organizations\Factory\Form\EmployeesFieldsetFactory;

/**
 * Test the factory of an EmployeesFieldset.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Factory
 * @group Organizations.Factory.Form
 */
class EmployeesFieldsetFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Does the factory implements the FactoryInterface?
     */
    public function testImplementsInterface()
    {
        $target = new EmployeesFieldsetFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $target);
    }

    /**
     * Does the factory returns an EmployeesFieldset?
     *
     * Tests additionally, if the javascript gets injected to the headscript helper.
     */
    public function testCreateServiceReturnsEmployeesFieldset()
    {
        $target = new EmployeesFieldsetFactory();

        $headscript = $this->getMockBuilder('\Zend\View\Helper\HeadScript')->disableOriginalConstructor()->getMock();
        $headscript->expects($this->once())
                   ->method('__call')
                   ->with('appendFile', array('Organizations/js/organizations.employees.js'))
                   ->willReturn(null);

        $basepath   = $this->getMockBuilder('\Zend\View\Helper\BasePath')->disableOriginalConstructor()->getMock();
        $basepath->expects($this->once())
                 ->method('__invoke')
                 ->with('Organizations/js/organizations.employees.js')
                 ->will($this->returnArgument(0));

        $helpers = $this->getMockBuilder('\Zend\View\HelperPluginManager')->disableOriginalConstructor()->getMock();
        $helpers->expects($this->exactly(2))
                ->method('get')
                ->withConsecutive(
                    array('headscript'),
                    array('basepath')
                )
                ->will($this->onConsecutiveCalls($headscript, $basepath));

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())
                 ->method('get')
                 ->with('ViewHelperManager')
                 ->willReturn($helpers);

        $fieldset = $target->__invoke($services,'irrelevant');

        $this->assertInstanceOf('\Organizations\Form\EmployeesFieldset', $fieldset);

        $hydrator = $fieldset->getHydrator();
        $this->assertInstanceOf('\Core\Entity\Hydrator\EntityHydrator', $hydrator);
        $this->assertTrue($hydrator->hasStrategy('employees'));
        $this->assertInstanceOf('\Core\Form\Hydrator\Strategy\CollectionStrategy', $hydrator->getStrategy('employees'));
    }


    
}