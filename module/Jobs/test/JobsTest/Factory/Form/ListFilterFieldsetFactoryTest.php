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

use Jobs\Factory\Form\ListFilterFieldsetExtendedFactory;
use Jobs\Form\ListFilterFieldset;

/**
 * Tests for \Jobs\Factory\Form\ListFilterFieldsetExtendedFactory
 * 
 * @covers \Jobs\Factory\Form\ListFilterFieldsetExtendedFactory
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Form
 */
class ListFilterFieldsetFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The "Class under Test"
     *
     * @var ListFilterFieldsetExtendedFactory
     */
    private $target;

    /**
     * The form element manager mock
     *
     * @var FormElementManager
     */
    private $formElements;

    public function setUp()
    {
        $this->target = new ListFilterFieldsetExtendedFactory();

        if ("testImplementsFactoryInterface" == $this->getName(/*withDataSet */ false)) { return; }

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $formElements = $this->getMockBuilder('\Zend\Form\FormElementManager')
                             ->disableOriginalConstructor()
                             ->getMock();

        $formElements->expects($this->once())->method('getServiceLocator')->willReturn($services);
        $this->formElements = $formElements;
    }


    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $target = new ListFilterFieldset();
        $this->assertInstanceOf('Jobs\Form\ListFilterFieldset', $target);
    }
}

