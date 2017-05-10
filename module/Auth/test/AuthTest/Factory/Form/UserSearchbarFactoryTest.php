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
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
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
    use ServiceManagerMockTrait;

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

        $formElements = $this->createPluginManagerMock([
            'text' => ['service' => $textElement, 'count_get' => 1],
                                                       ]);


        $services = $this->getServiceManagerMock([
            'ViewHelperManager' => ['service' => $helpers, 'count_get' => 1],
            'forms' => $formElements,
                                                 ]);

        $formElements->setServiceLocator($services, 1);
        $element = $factory->createService($formElements);

        $this->assertInstanceOf('\Zend\Form\Element\Text', $element);
        $this->assertEquals('usersearchbar', $element->getAttribute('class'), 'Class attributes\' value does not contain the expected value!');
    }
}
