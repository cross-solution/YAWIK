<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace AuthTest\Factory\Form;

use Auth\Factory\Form\Element\UserSearchbarFactory;
use Zend\Form\Element\Text;

/**
 * Tests for UserSearchbarFactory
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Auth
 * @group Auth.Factory
 * @group Auth.Factory.Form
 */
class UserSearchbarFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testImplementsFactoryInterface()
    {
        $factory = new UserSearchbarFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', $factory);
    }

    public function testCreatesTextInputElementAndInjectsJavascriptInHeadscriptViewHelper()
    {
        $factory = new UserSearchbarFactory();

        $textElement = new Text();

        $headscript = $this->getMockBuilder('\Zend\View\Helper\HeadScript')->disableOriginalConstructor()->getMock();
        $headscript->expects($this->once())->method('__call')->with('appendFile', array('Auth/js/form.usersearchbar.js'));

        $basepath = $this->getMockBuilder('\Zend\View\Helper\BasePath')->disableOriginalConstructor()->getMock();
        $basepath->expects($this->once())->method('__invoke')->with('Auth/js/form.usersearchbar.js')
                 ->will($this->returnArgument(0));

        $helpers = $this->getMockBuilder('\Zend\View\HelperPluginManager')->disableOriginalConstructor()->getMock();
        $helpers->expects($this->exactly(2))
                ->method('get')
                ->withConsecutive(
                    array('headscript'),
                    array('basepath')
                )
                ->will($this->onConsecutiveCalls($headscript, $basepath));

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->once())->method('get')->with('ViewHelperManager')->willReturn($helpers);

        $formElements = $this->getMockBuilder('\Zend\Form\FormElementManager')->disableOriginalConstructor()->getMock();
        $formElements->expects($this->once())->method('getServiceLocator')->willReturn($services);
        $formElements->expects($this->once())->method('get')->with('text')->willReturn($textElement);

        $element = $factory->createService($formElements);

        $this->assertInstanceOf('\Zend\Form\Element\Text', $element);
        $this->assertEquals('usersearchbar', $element->getAttribute('class'), 'Class attributes\' value does not contain the expected value!');
    }
    
}