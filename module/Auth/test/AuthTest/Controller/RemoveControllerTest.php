<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace AuthTest\Controller;

use PHPUnit\Framework\TestCase;

use Auth\Controller\RemoveController;
use Auth\Dependency\Manager as Dependencies;
use Auth\AuthenticationService;
use Auth\Entity\User;
use Auth\Entity\Status;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Http\PhpEnvironment\Response;

/**
 *
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @coversDefaultClass \Auth\Controller\RemoveController
 */
class RemoveControllerTest extends TestCase
{
    
    /**
     * @var RemoveController
     */
    protected $controller;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependencies;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authService;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRepository;

    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->dependencies = $this->getMockBuilder(Dependencies::class)
            ->disableOriginalConstructor()
            ->getMock();
    
        $this->authService = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRepository = $this->getMockBuilder(\Auth\Repository\User::class)
            ->disableOriginalConstructor()
            ->getMock();
    
        $this->controller = new RemoveController($this->dependencies, $this->authService, $this->userRepository);
    }
    
    /**
     * @covers ::__construct
     * @covers ::indexAction
     */
    public function testIndexActionListsDependencies()
    {
        $lists = ['someListItem'];
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $this->dependencies->expects($this->once())
            ->method('getLists')
            ->willReturn($lists);
        
        $this->authService->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $result = $this->controller->indexAction();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('lists', $result);
        $this->assertSame($lists, $result['lists']);
        $this->assertArrayHasKey('user', $result);
        $this->assertSame($user, $result['user']);
        $this->assertArrayHasKey('limit', $result);
        $this->assertIsInt($result['limit']);
        $this->assertGreaterThan(0, $result['limit']);
        $this->assertArrayHasKey('error', $result);
        $this->assertFalse($result['error']);
    }
    
    /**
     * @covers ::__construct
     * @covers ::indexAction
     */
    public function testIndexActionRemoveDependenciesSuccessfully()
    {
        $response = $this->controller->getResponse();
        
        $redirect = $this->getMockBuilder(Redirect::class)
            ->getMock();
        $redirect->expects($this->once())
            ->method('toRoute')
            ->with($this->equalTo('lang'))
            ->willReturn($response);
        
        $params = $this->getMockBuilder(Params::class)
            ->setMethods(['fromPost'])
            ->getMock();
        $params->expects($this->once())
            ->method('fromPost')
            ->with($this->equalTo('confirm'))
            ->willReturn('1');
        
        $pluginManager = $this->controller->getPluginManager();
        $pluginManager->setService('redirect', $redirect);
        $pluginManager->setService('params', $params);
        
        $user = $this->getMockBuilder(User::class)
            ->getMock();

        
        $this->dependencies->expects($this->once())
            ->method('removeItems')
            ->with($this->equalTo($user))
            ->willReturn(true);
        
        $this->authService->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $this->authService->expects($this->once())
            ->method('clearIdentity');

        $this->userRepository->expects($this->once())->method('remove')->with($user);

        $this->assertSame($response, $this->controller->indexAction());
    }
    
    /**
     * @covers ::__construct
     * @covers ::indexAction
     */
    public function testIndexActionRemoveDependenciesUnsuccessfully()
    {
        $lists = ['someListItem'];
        $params = $this->getMockBuilder(Params::class)
            ->setMethods(['fromPost'])
            ->getMock();
        $params->expects($this->once())
            ->method('fromPost')
            ->with($this->equalTo('confirm'))
            ->willReturn('1');
        
        $this->controller->getPluginManager()
            ->setService('params', $params);
        
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->never())
            ->method('setStatus');
        
        $this->dependencies->expects($this->once())
            ->method('getLists')
            ->willReturn($lists);
        $this->dependencies->expects($this->once())
            ->method('removeItems')
            ->with($this->equalTo($user))
            ->willReturn(false);
        
        $this->authService->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $this->authService->expects($this->never())
            ->method('clearIdentity');
        $this->userRepository->expects($this->never())->method('remove')->with($user);
        
        $result = $this->controller->indexAction();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('lists', $result);
        $this->assertSame($lists, $result['lists']);
        $this->assertArrayHasKey('user', $result);
        $this->assertSame($user, $result['user']);
        $this->assertArrayHasKey('limit', $result);
        $this->assertIsInt($result['limit']);
        $this->assertGreaterThan(0, $result['limit']);
        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error']);
    }
}
