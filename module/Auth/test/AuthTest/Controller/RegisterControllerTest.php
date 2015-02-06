<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller;

use Auth\Controller\RegisterController;
use Auth\Form\RegisterInputFilter;
use Auth\Service\Exception;
use Test\Bootstrap;
use Core\Controller\Plugin\Notification;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\Stdlib\Parameters;

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

    public function setUp()
    {
        $this->init('register');

        $this->formMock = $this->getMock('Auth\Form\Register');

        $this->serviceMock = $this->getMockBuilder('Auth\Service\Register')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMock('Zend\Log\LoggerInterface');

        $this->controller = new RegisterController($this->formMock, $this->serviceMock, $loggerMock);
        $this->controller->setEvent($this->event);

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = clone Bootstrap::getServiceManager()->get('ControllerPluginManager');
        $this->controller->setPluginManager($controllerPluginManager);
    }

    public function testIndexAction_WithGetRequest()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $result = $this->controller->dispatch($request);
        $expected = array(
            'form' => $this->formMock
        );
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);
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

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'Please fill form correctly'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
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

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

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

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

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

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

        // TODO: reactivate
        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_SUCCESS);
        //$expectedMessages = array(
        //    'An Email with an activation link has been sent, please try to check your email box'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }
}