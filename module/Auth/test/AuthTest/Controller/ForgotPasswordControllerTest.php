<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller;

use Auth\Controller\ForgotPasswordController;
use Auth\Form\ForgotPasswordInputFilter;
use Auth\Service\Exception;
use Test\Bootstrap;
use Core\Controller\Plugin\Notification;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\Stdlib\Parameters;

class ForgotPasswordControllerTest extends AbstractControllerTestCase
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
        $this->init('forgot-password');

        $this->formMock = $this->getMock('Auth\Form\ForgotPassword');

        $this->serviceMock = $this->getMockBuilder('Auth\Service\ForgotPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMock('Zend\Log\LoggerInterface');

        $this->controller = new ForgotPasswordController($this->formMock, $this->serviceMock, $loggerMock);
        $this->controller->setEvent($this->event);

        /** @var PluginManager $controllerPluginManager */
        $servicemanager = clone Bootstrap::getServiceManager();
        $controllerPluginManager = $servicemanager->get('ControllerPluginManager');
        $this->controller->setServiceLocator($servicemanager);
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

        $mvcEvent = new MvcEvent();
        $notifications = $this->controller->getServiceLocator()->get('coreListenerNotification');
        $notifications->reset()->renderHTML($mvcEvent);

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

    public function testIndexAction_WithPostRequest_WhenUserCannotBeFoundByUsernameOrEmail()
    {
        $postData = array('identity' => uniqid('identity'));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn(new ForgotPasswordInputFilter());

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->willThrowException(new Exception\UserNotFoundException());

        $result = $this->controller->dispatch($request);

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'User cannot be found for specified username or email'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithPostRequest_WhenUserDoesNotHaveAnEmail()
    {
        $postData = array('identity' => uniqid('identity'));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn(new ForgotPasswordInputFilter());

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->willThrowException(new Exception\UserDoesNotHaveAnEmailException());

        $result = $this->controller->dispatch($request);

        $mvcEvent = new MvcEvent();
        $notifications = $this->controller->getServiceLocator()->get('coreListenerNotification');
        $notifications->reset()->renderHTML($mvcEvent);

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

        // @TODO: Fix this, the messages already have been transferred to somewhere
        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array();
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithPostRequest_WhenUnexpectedExceptionHasOccurred()
    {
        $postData = array('identity' => uniqid('identity'));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn(new ForgotPasswordInputFilter());

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->willThrowException(new \LogicException());

        $result = $this->controller->dispatch($request);

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'An unexpected error has occurred, please contact your system administrator'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithPostRequest()
    {
        $postData = array('identity' => uniqid('identity'));

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->formMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn(new ForgotPasswordInputFilter());

        $this->serviceMock->expects($this->once())
            ->method('proceed');

        $result = $this->controller->dispatch($request);

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_SUCCESS);
        //$expectedMessages = array(
        //    'Mail with link for reset password has been sent, please try to check your email box'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }
}