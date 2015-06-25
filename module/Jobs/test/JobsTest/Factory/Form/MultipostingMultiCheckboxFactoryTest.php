<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
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
     * @testdox Extends \Jobs\Factory\Form\MultipostingSelectFactory
     */
    public function testExtendsMultiPostingSelectFactory()
    {
        $target = new MultipostingMultiCheckboxFactory();

        $this->assertInstanceOf('\Jobs\Factory\Form\MultipostingSelectFactory', $target);
    }

    /**
     * Allows creation of a multiposting multicheckbox element
     */
    public function testCreateService()
    {
        $select = $this->getMockBuilder('Jobs\Form\MultipostingSelect')->disableOriginalConstructor()->getMock();
        $select->expects($this->once())
               ->method('setViewPartial')->with('jobs/form/multiposting-checkboxes');

        $select->expects($this->once())
               ->method('setHeadscripts')->with(array('Jobs/js/form.multiposting-checkboxes.js'));

        $sm = $this->getMockForAbstractClass('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm->expects($this->once())
           ->method('get')->with('Jobs/MultipostingSelectElement')
           ->willReturn($select);

        $target = new MultipostingMultiCheckboxFactory();

        $actual = $target->createService($sm);

        $this->assertSame($select, $actual);
    }
}

