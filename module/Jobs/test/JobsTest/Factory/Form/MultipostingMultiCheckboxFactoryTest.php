<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Form;

use Jobs\Factory\Form\MultipostingMultiCheckboxFactory;

/**
 * Tests for \Jobs\Factory\Form\MultipostingMultiCheckboxFactory
 * 
 * @covers \Jobs\Factory\Form\MultipostingMultiCheckboxFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Form
 */
class MultipostingMultiCheckboxFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $target = new MultipostingMultiCheckboxFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $target);
    }

    public function testAllowsSettingAndGettingOfParentFactory()
    {
        $target = new MultipostingMultiCheckboxFactory();
        $factory = $this->getMockForAbstractClass('\Zend\ServiceManager\FactoryInterface');

        $this->assertInstanceOf('\Jobs\Factory\Form\MultipostingSelectFactory', $target->getParentFactory(), 'Wrong default parent factory ');
        $this->assertSame($target, $target->setParentFactory($factory), 'Fluent interface broken');
        $this->assertSame($factory, $target->getParentFactory());
    }

    /**
     * @testdox Allows creation of a multiposting multicheckbox element
     */
    public function testInvokation()
    {
        $select = $this->getMockBuilder('Jobs\Form\MultipostingSelect')->disableOriginalConstructor()->getMock();
        $select->expects($this->once())
               ->method('setViewPartial')->with('jobs/form/multiposting-checkboxes');

        $select->expects($this->once())
               ->method('setHeadscripts')->with(array('Jobs/js/form.multiposting-checkboxes.js'));

        $factory = $this->getMockBuilder('\Zend\ServiceManager\FactoryInterface')
                        ->setMethods(array('__invoke'))
                        ->getMockForAbstractClass()
        ;
        $factory
	        ->expects($this->once())
	        ->method('__invoke')
	        ->willReturn($select)
        ;


        $sm = $this->getMockForAbstractClass('\Zend\ServiceManager\ServiceLocatorInterface');

        $target = new MultipostingMultiCheckboxFactory();
        $target->setParentFactory($factory);

        $actual = $target($sm,'irrelevant');

        $this->assertSame($select, $actual);
    }
}
