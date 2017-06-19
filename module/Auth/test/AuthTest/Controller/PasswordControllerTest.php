<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller;

use Test\Bootstrap;
use Auth\AuthenticationService;
use Auth\Controller\PasswordController;
use Auth\Form\UserPassword;
use AuthTest\Entity\Provider\UserEntityProvider;
use Core\Repository\RepositoryService;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Stdlib\Parameters;

class PasswordControllerTest extends AbstractControllerTestCase
{
    /**
     * @var MockObject|UserPassword
     */
    private $formMock;

    /**
     * @var MockObject|AuthenticationService
     */
    private $authenticationServiceMock;

    /**
     * @var MockObject|RepositoryService
     */
    private $repositoriesMock;

    public function setUp()
    {
        $this->init('password');

        $this->formMock = $this->getMockBuilder('Auth\Form\UserPassword')
            ->getMock();

        $this->authenticationServiceMock = $this->getMockBuilder('Auth\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new PasswordController(
            $this->authenticationServiceMock,
            $this->formMock,
            $this->repositoriesMock
        );

        $this->controller->setEvent($this->event);

        $serviceManager = Bootstrap::getServiceManager();
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');
        $this->controller->setPluginManager($controllerPluginManager);
    }

    public function testIndexAction_WithGetRequest()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $userEntity = UserEntityProvider::createEntityWithRandomData();

        $this->authenticationServiceMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userEntity);

        $this->formMock->expects($this->once())
            ->method('bind')
            ->with($userEntity);

        $result = $this->controller->dispatch($request);

        $expected = array(
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);
    }

    public function testIndexAction_WithGetRequest_WhenUserIsNotLogged()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $this->authenticationServiceMock->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->controller->dispatch($request);
        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertRedirectTo('/en');
    }

    public function testIndexAction_WithPostRequest_WhenDataIsInvalid()
    {
        $postData = array();

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $userEntity = UserEntityProvider::createEntityWithRandomData();

        $this->authenticationServiceMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userEntity);

        $this->formMock->expects($this->once())
            ->method('bind')
            ->with($userEntity);

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->formMock->expects($this->once())
            ->method('getMessages')
            ->willReturn(array('Some error message'));

        $result = $this->controller->dispatch($request);

        $expected = array(
            'valid' => false,
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);
    }

    public function testIndexAction_WithPostRequest()
    {
        $postData = array('valid data');

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setPost(new Parameters($postData));

        $userEntity = UserEntityProvider::createEntityWithRandomData();

        $this->authenticationServiceMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userEntity);

        $this->formMock->expects($this->once())
            ->method('bind')
            ->with($userEntity);

        $this->formMock->expects($this->once())
            ->method('setData')
            ->with($postData);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->repositoriesMock->expects($this->once())
            ->method('store')
            ->with($userEntity);

        $result = $this->controller->dispatch($request);

        $expected = array(
            'valid' => true,
            'form' => $this->formMock
        );

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertSame($expected, $result);
    }
}
