<?php

namespace CoreTest\Mail;

use Core\Mail\HTMLTemplateMessage;
use Core\Mail\MailService as CoreMailService;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use Zend\Mvc\View\Http\ViewManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\View;


/**
 * Class HTMLTemplateMessageTest
 *
 * @package     CoreTest\Mail
 * @ticket      222
 * @covers      \Core\Mail\HTMLTemplateMessage
 */
class HTMLTemplateMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class Under Test
     *
     * @var HTMLTemplateMessage
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;


    public function setUp()
    {
        /* @var ServiceLocatorInterface $serviceManager */
        $serviceManager = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        // mock setup
        $viewManager = $this->getMockBuilder(ViewManager::class)->getMock();
        $viewResolver = $this->getMockForAbstractClass(ResolverInterface::class);
        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $view = $this->getMockBuilder(View::class)->getMock();
        $viewManager
            ->method('getView')
            ->willReturn($view);
        $mvcEvent = $this->getMockBuilder(MvcEvent::class)->getMock();
        $routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();
        $routeMatch->method('getParam')->willReturn('en');

        $mvcEvent
            ->method('getRouteMatch')
            ->willReturn($routeMatch);

        $application
            ->method('getMvcEvent')
            ->willReturn($mvcEvent);

        $serviceManager
            ->method('get')
            ->willReturnMap([
                ['ViewManager', $viewManager],
                ['ViewResolver', $viewResolver],
                ['Application', $application]
            ]);

        $this->resolver = $viewResolver;
        $this->view = $view;
        $this->serviceManager = $serviceManager;
        $this->target = new HTMLTemplateMessage($serviceManager);
    }

    public function testSetterAndGetter()
    {
        $target = $this->target;

        $this->assertEquals(null, $target->foo, '::__get() returns null if variable is not set');
        $target->foo = 'bar';
        $this->assertEquals('bar', $target->foo);

        unset($target->foo);
        $this->assertEquals(null, $target->foo, '::__unset() should unset variable from object');
    }

    public function testSetVariable()
    {
        $target = $this->target;

        $target->setVariable('hello', 'world');
        $this->assertEquals(
            'world',
            $target->getVariable('hello'),
            '::setVariable() should set variable'
        );
        $this->assertEquals('world', $target->hello, '::setVariable() should set variable');


    }

    public function testGetVariable()
    {
        $target = $this->target;

        $this->assertEquals(
            'bar',
            $target->getVariable('foo', 'bar'),
            '::getVariable() should return default value if variable not set'
        );
    }

    public function testSetVariables()
    {
        $target = $this->target;

        $variables = [
            'foo' => 'bar',
        ];

        $target->setVariables($variables);
        $this->assertEquals('bar', $target->getVariable('foo'));

        $ob = new \EmptyIterator();
        $target->setVariables($ob, true);
        $this->assertEquals([], $target->getVariables(), '::setVariables() should convert non \ArrayAccess object into array');
        $this->assertEquals(
            null,
            $target->getVariable('foo'),
            '::setVariables() should overwrite any value if passed overwrite value is true'
        );

        $this->setExpectedException(
            \InvalidArgumentException::class
        );
        $target->setVariables($target);
    }

    public function testClearVariables()
    {
        $target = $this->target;
        $target->setVariables(['foo' => 'bar']);

        $target->clearVariables();
        $this->assertEquals(null, $target->getVariable('foo'));
    }

    public function testSetAndGetTemplate()
    {
        $target = $this->target;

        $this->assertEquals(
            null,
            $target->getTemplate(),
            '::getTemplate() should return null when not set'
        );
        $target->setTemplate('some');
        $this->assertEquals(
            'some',
            $target->getTemplate(),
            '::getTemplate() should return template value'
        );
    }

    public function testStaticFactory()
    {
        $service = $this->getMockBuilder(CoreMailService::class)
            ->disableOriginalConstructor()
            ->getMock();

        /*$service
            ->expects($this->once())
            ->method('getServiceLocator')
            ->willReturn($this->serviceManager);*/

        $this->assertInstanceOf(
            HTMLTemplateMessage::class,
            HTMLTemplateMessage::factory($service),
            '::factory() should create new HTMLTemplateMessage class'
        );
    }

    public function testGetBodyTextShouldRenderTemplateByLanguage()
    {
        $target = $this->target;
        $viewResolver = $this->resolver;
        $view = $this->view;

        $target->setTemplate('template');
        // resolver expectation
        $viewResolver
            ->method('resolve')
            ->willReturn(true);
        $view
            ->method('render')
            ->with($this->callback(function (ViewModel $subject) {
                return $subject->getTemplate() === 'template.en';
            }));
        $target->getBodyText();
    }

    public function testGetBodyTextShouldNotRenderTemplateByLanguage()
    {
        $target = $this->target;
        $viewResolver = $this->resolver;
        $view = $this->view;

        $target->setTemplate('template');
        // resolver expectation
        $viewResolver
            ->method('resolve')
            ->willReturn(false);
        $view
            ->method('render')
            ->with($this->callback(function (ViewModel $subject) {
                return $subject->getTemplate() === 'template';
            }));
        $target->getBodyText();
    }

    /**
     * Mail body shall come from Template
     *
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    mail body shall come from Template.
     */
    public function testGetBodyThrowsExceptionWhenMailBodyIsNotSetByTemplate()
    {
        $target = $this->target;
        $target->setBody('Hello World');

        $target->getBodyText();
    }
}
