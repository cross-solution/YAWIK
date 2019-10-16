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

use Auth\Controller\RegisterConfirmationController;
use Auth\Service\Exception;
use CoreTest\Bootstrap;
use Core\Controller\Plugin\Notification;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;

/**
 * Class RegisterConfirmationControllerTest
 *
 * @package AuthTest\Controller
 * @runTestsInSeparateProcesses
 * @covers \Auth\Controller\RegisterConfirmationController
 * @group Auth
 * @group Auth.Controller
 */

class RegisterConfirmationControllerTest extends AbstractControllerTestCase
{
    /**
     * @var MockObject
     */
    private $serviceMock;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    protected function setUp(): void
    {
        $this->init('register-confirmation');

        $this->serviceMock = $this->getMockBuilder('Auth\Service\RegisterConfirmation')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')->getMockForAbstractClass();

        $this->controller = new RegisterConfirmationController($this->serviceMock, $loggerMock);
        $this->controller->setEvent($this->event);

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = clone Bootstrap::getServiceManager()->get('ControllerPluginManager');
        $this->controller->setPluginManager($controllerPluginManager);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIndexAction_WithGetRequest()
    {
        $userId = uniqid('user');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId);

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/my/password');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIndexAction_WithGetRequest_WhenCannotFoundUserBySpecifiedToken()
    {
        $userId = uniqid('user');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId)
            ->willThrowException(new Exception\UserNotFoundException());

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/auth/register');

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'User cannot be found'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIndexAction_WithGetRequest_WhenUnexpectedExceptionOccurred()
    {
        $userId = uniqid('user');

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->routeMatch->setParam('userId', $userId);

        $this->serviceMock->expects($this->once())
            ->method('proceed')
            ->with($userId)
            ->willThrowException(new \LogicException());

        $this->controller->dispatch($request);

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en/auth/register');

        //$fm = $this->controller->flashMessenger();
        //$fm->setNamespace(Notification::NAMESPACE_DANGER);
        //$expectedMessages = array(
        //    'An unexpected error has occurred, please contact your system administrator'
        //);
        //$this->assertSame($expectedMessages, $fm->getCurrentMessages());
    }
}
