<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form\Service;

use Core\Form\Service\InjectHeadscriptInitializer;

/**
 * Tests for InjectHeadscriptInitializer
 *
 * @covers \Core\Form\Service\InjectHeadscriptInitializer
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 * @group Core.Form.Service
 */
class InjectHeadscriptInitializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Class under Test
     *
     * @var InjectHeadscriptInitializer
     */
    private $target;

    /**
     *
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formElementManagerMock;

    public function setUp()
    {
        $this->target = new InjectHeadscriptInitializer();

        $name = $this->getName(false);
        if ('testImplementsInitializerInterface' == $name) {
            return;
        }

        $this->formElementManagerMock = $this->getMockbuilder('\Zend\Form\FormElementManager')->disableOriginalConstructor()->getMock();
    }

    /**
     * @testdox Implements \Zend\ServiceManager\InitializerInterface
     */
    public function testImplementsInitializerInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\InitializerInterface', $this->target);
    }

    /**
     * @testdox Does nothing if passed object does not implement \Core\Form\HeadscriptProviderInterface
     */
    public function testDoesNothingIfPassedObjectDoesNotImplementHeadscriptProviderInterface()
    {
        $this->formElementManagerMock->expects($this->never())->method('getServiceLocator');
        $instance = $this->getMock('stdClass');
        $instance->expects($this->never())->method('getHeadscripts');

        $this->target->initialize($instance, $this->formElementManagerMock);
    }

    /**
     * @testdox Does nothing if passed object returns no array or an empty one.
     */
    public function testDoesNothingIfPassedObjectDoesProvideEmptyHeadscriptsArray()
    {
        $this->formElementManagerMock->expects($this->never())->method('getServiceLocator');
        $instance = $this->getMockForAbstractClass('\Core\Form\HeadscriptProviderInterface');
        $instance->expects($this->exactly(2))
                 ->method('getHeadscripts')
                 ->will($this->onConsecutiveCalls('notanarray', array()));

        $this->target->initialize($instance, $this->formElementManagerMock);
        $this->target->initialize($instance, $this->formElementManagerMock);
    }

    public function testInjectsScriptsToTheHeadscriptViewHelper()
    {
        $basepath = $this->getMockBuilder('\Zend\View\Helper\BasePath')
                         ->disableOriginalConstructor()->getMock();

        $basepath->expects($this->any())
                 ->method('__invoke')->will($this->returnArgument(0));


        $headscript = $this->getMockBuilder('\Zend\View\Helper\HeadScript')
                           ->disableOriginalConstructor()->getMock();

        $scripts = array('test/script.js', 'yetanother/test/script.tst');

        $instance = $this->getMockForAbstractClass('\Core\Form\HeadscriptProviderInterface');
        $instance->expects($this->once())->method('getHeadscripts')->willReturn($scripts);
        $this->instanceMock = $instance;

        $headscript->expects($this->exactly(2))
                   ->method('__call')->withConsecutive(
                   array('appendFile', array($scripts[0])),
                   array('appendFile', array($scripts[1]))
            );

        $helpers = $this->getMockBuilder('\Zend\View\HelperPluginManager')
                        ->disableOriginalConstructor()
                        ->getMock();


        $helpers->expects($this->exactly(2))->method('get')
                ->withConsecutive(array('basepath'), array('headscript'))
                ->will($this->onConsecutiveCalls($basepath, $headscript));

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->exactly(1))->method('get')->with('ViewHelperManager')->willReturn($helpers);
        $this->formElementManagerMock->expects($this->once())->method('getServiceLocator')->willReturn($services);

        $this->target->initialize($instance, $this->formElementManagerMock);
    }


}