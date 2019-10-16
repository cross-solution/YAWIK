<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller;

use PHPUnit\Framework\TestCase;

use Auth\Controller\RegisterController;
use Auth\Form\RegisterInputFilter;
use Auth\Options\CaptchaOptions;
use Auth\Service\Exception;
use Auth\Options\ModuleOptions;
use CoreTest\Bootstrap;
use Core\Controller\Plugin\Notification;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

class RegisterControllerTest extends AbstractControllerTestCase
{
    /**
     * @var MockObject
     */
    private $formMock;

    /**
     * @var MockObject
     */
    private $serviceMock;

    /**
     * @var MockObject
     */
    private $paramsMock;

    protected function setUp(): void
    {
        $this->init('register');

        $captureOptions = new CaptchaOptions();
        $this->formMock = $this->getMockBuilder('Auth\Form\Register')
            ->setConstructorArgs([null, $captureOptions])
            ->getMock();


        $this->serviceMock = $this->getMockBuilder('Auth\Service\Register')
                                  ->disableOriginalConstructor()
                                  ->getMock();


        $this->paramsMock = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\Params')
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $options = new ModuleOptions();

        $this->controller = new RegisterController($this->formMock, $this->serviceMock, $loggerMock, $options);
        $this->controller->setEvent($this->event);

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = clone Bootstrap::getServiceManager()->get('ControllerPluginManager');
        $controllerPluginManager->setService('params', $this->paramsMock);
        $this->controller->setPluginManager($controllerPluginManager);
    }

    public function testIndexAction_WithGetRequest()
    {
        $register = $this->getMockBuilder('Zend\Form\Fieldset')->getMock();
        $role = $this->getMockBuilder('Zend\Form\Element\Hidden')->getMock();

        $this->formMock->expects($this->once())->method('get')->with('register')->willReturn($register);

        $register->expects($this->once())->method('get')->with('role')->willReturn($role);

        $role->expects($this->once())->method('setValue')->willReturn($role);

        $this->paramsMock->expects($this->once())->method('__invoke')->with('role')->willReturn('user');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $result = $this->controller->dispatch($request);

        $expected = new ViewModel();
        $expected->setVariable('form', $this->formMock);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertEquals($expected, $result);
    }

    public function testIndexAction_WithPostRequest_WhenDataIsInvalid()
    {
        $postData = array();

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $result = $this->controller->dispatch($request);

        $fm = $this->controller->flashMessenger();
        $fm->setNamespace(Notification::NAMESPACE_DANGER);
        $expectedMessages = ['Please fill form correctly'];

        /* @todo: fix that */
        #$this->assertSame($expectedMessages, $fm->getCurrentMessages());

        $expected = new ViewModel();
        $expected->setVariable('form', $this->formMock);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertEquals($expected, $result);
    }

    public function testIndexAction_WithPostRequest_WhenUserAlreadyExists()
    {
        $postData = array(
            'name' => uniqid('name'),
            'email' => uniqid('email') . '@' . uniqid('host') . '.com.pl'
        );

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $registerInputFilter = new RegisterInputFilter();
        $registerInputFilter->add(array('name' => 'captcha'));

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn($registerInputFilter);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->willThrowException(new Exception\UserAlreadyExistsException());

        $result = $this->controller->dispatch($request);

        $expected = new ViewModel();
        $expected->setTemplate('');
        $expected->setVariable('form', $this->formMock);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertEquals($expected, $result);

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'User with this email address already exists'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithPostRequest_WhenUnexpectedExceptionHasOccurred()
    {
        $postData = array(
            'name' => uniqid('name'),
            'email' => uniqid('email') . '@' . uniqid('host') . '.com.pl'
        );

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $registerInputFilter = new RegisterInputFilter();
        $registerInputFilter->add(array('name' => 'captcha'));

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn($registerInputFilter);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->willThrowException(new \LogicException());

        $result = $this->controller->dispatch($request);

        $expected = new ViewModel();
        $expected->setVariable('form', $this->formMock);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertEquals($expected, $result);

        // TODO: reactivate
        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'An unexpected error has occurred, please contact your system administrator'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithValidPostRequest()
    {
        $postData = array(
            'name' => uniqid('name'),
            'email' => uniqid('email') . '@' . uniqid('host') . '.com.pl'
        );

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $registerInputFilter = new RegisterInputFilter();
        $registerInputFilter->add(array('name' => 'captcha'));

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn($registerInputFilter);

        $this->serviceMock->expects($this->once())
            ->method('proceed');

        $result = $this->controller->dispatch($request);

        $expected = new ViewModel();
        $expected->setTemplate('auth/register/completed');
        $expected->setVariable('form', $this->formMock);

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertEquals($expected, $result);

        // @todo: fix that
        $fm = $this->controller->flashMessenger();
        $fm->setNamespace(Notification::NAMESPACE_SUCCESS);
        $expectedMessages = array(
            'An Email with an activation link has been sent, please try to check your email box'
        );
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }
}
