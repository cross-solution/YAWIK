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

use Auth\Controller\GotoResetPasswordController;
use Auth\Service\Exception;
use CoreTest\Bootstrap;
use Core\Controller\Plugin\Notification;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;

class GotoResetPasswordControllerTest extends AbstractControllerTestCase
{
    /**
     * @var MockObject
     */
    private $serviceMock;

    protected function setUp(): void
    {
        $this->init('goto-reset-password');

        $this->serviceMock = $this->getMockBuilder('Auth\Service\GotoResetPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $this->controller = new GotoResetPasswordController($this->serviceMock, $loggerMock);
        $this->controller->setEvent($this->event);

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = clone Bootstrap::getServiceManager()->get('ControllerPluginManager');
        $this->controller->setPluginManager($controllerPluginManager);
    }

    public function testIndexAction_WithGetRequest()
    {
        $userId = uniqid('user');
        $token = uniqid('token');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId)
            ->setParam('token', $token);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId, $token);

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/my/password');
    }

    public function testIndexAction_WithGetRequest_WhenTokenExpired()
    {
        $userId = uniqid('user');
        $token = uniqid('token');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId)
            ->setParam('token', $token);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId, $token)
            ->willThrowException(new Exception\TokenExpirationDateExpiredException());

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/auth/forgot-password');

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'Cannot proceed, token expired'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithGetRequest_WhenCannotFoundUserBySpecifiedToken()
    {
        $userId = uniqid('user');
        $token = uniqid('token');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId)
            ->setParam('token', $token);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId, $token)
            ->willThrowException(new Exception\UserNotFoundException());

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/auth/forgot-password');

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'User cannot be found for specified token'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    public function testIndexAction_WithGetRequest_WhenUnexpectedExceptionOccurred()
    {
        $userId = uniqid('user');
        $token = uniqid('token');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId)
            ->setParam('token', $token);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId, $token)
            ->willThrowException(new \LogicException());

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/auth/forgot-password');

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'An unexpected error has occurred, please contact your system administrator'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }
}
