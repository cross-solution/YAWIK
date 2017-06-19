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

use Organizations\Factory\Form\EmployeeFieldsetFactory;

/**
 * Tests the EmployeeFieldsetFactory
 *
 * @covers \Organizations\Factory\Form\EmployeeFieldsetFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Organizations
 * @group Organizations.Factory
 * @group Organizations.Factory.Form
 */
class EmployeeFieldsetFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Does the factory implements the FActoryInterface?
     */
    public function testImplementsInterface()
    {
        $target = new EmployeeFieldsetFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $target);
    }

    /**
     * Does the factory returns an instance of EmployeeFieldset?
     *
     * Are all dependencies configured properly and injected?
     */
    public function testInvokation()
    {
        $target = new EmployeeFieldsetFactory();

        $users = new \stdClass(); // only dummy object needed.

        $repos = $this->getMockBuilder('\Core\Repository\RepositoryService')->disableOriginalConstructor()->getMock();
        $repos->expects($this->once())
              ->method('get')->with('Auth/User')->willReturn($users);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->once())
                 ->method('get')->with('repositories')->willReturn($repos);
	    
        $fieldset = $target->__invoke($services,'irrelevant');

        $this->assertInstanceOf('\Organizations\Form\EmployeeFieldset', $fieldset);

        $hydrator = $fieldset->getHydrator();
        $this->assertTrue($hydrator->hasStrategy('user'));
        $this->assertInstanceOf('\Zend\Hydrator\Strategy\ClosureStrategy', $hydrator->getStrategy('user'));

        $this->assertTrue($hydrator->hasStrategy('permissions'));
        $this->assertInstanceOf('\Zend\Hydrator\Strategy\ClosureStrategy', $hydrator->getStrategy('permissions'));

        $object = $fieldset->getObject();
        $this->assertInstanceOf('\Organizations\Entity\Employee', $object);
    }
}