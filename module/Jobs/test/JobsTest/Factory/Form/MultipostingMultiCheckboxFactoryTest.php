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

use PHPUnit\Framework\TestCase;

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
class MultipostingMultiCheckboxFactoryTest extends TestCase
{
    /**
     * @testdox Implements \Laminas\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $target = new MultipostingMultiCheckboxFactory();

        $this->assertInstanceOf('\Laminas\ServiceManager\Factory\FactoryInterface', $target);
    }

    public function testAllowsSettingAndGettingOfParentFactory()
    {
        $target = new MultipostingMultiCheckboxFactory();
        $factory = $this->getMockForAbstractClass('\Laminas\ServiceManager\FactoryInterface');

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
               ->method('setHeadscripts')->with(array('modules/Jobs/js/form.multiposting-checkboxes.js'));

        $factory = $this->getMockBuilder('\Laminas\ServiceManager\FactoryInterface')
                        ->setMethods(array('__invoke'))
                        ->getMockForAbstractClass()
        ;
        $factory
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($select)
        ;


        $sm = $this->getMockForAbstractClass('\Laminas\ServiceManager\ServiceLocatorInterface');

        $target = new MultipostingMultiCheckboxFactory();
        $target->setParentFactory($factory);

        $actual = $target($sm, 'irrelevant');

        $this->assertSame($select, $actual);
    }
}
