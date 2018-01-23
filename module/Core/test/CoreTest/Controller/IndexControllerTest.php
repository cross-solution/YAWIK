<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller;

use Auth\Controller\Plugin\Auth;
use Zend\Http\Response;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\Plugin\Forward;
use Zend\Mvc\Controller\Plugin\Layout;
use Zend\Mvc\Controller\PluginManager;
use Core\Controller\IndexController;
use Core\Listener\DefaultListener;
use Core\Controller\Plugin\Config;
use Interop\Container\ContainerInterface;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

class IndexControllerTest extends AbstractControllerTestCase
{
    private $config;

    private $defaultListener;

    private $moduleManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $pluginsMock;

    public function testFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $listener = $this->createMock(DefaultListener::class);
        $config = $this->createMock(Config::class);
        $container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['DefaultListeners',$listener],
                ['config',$config]
            ]);
        $ob = IndexController::factory($container);

        $this->assertInstanceOf(IndexController::class,$ob);
    }

    /**
     * @return IndexController
     */
    public function setupTarget($auth=null,$layout=null,$forward=null,$config=null)
    {
        $auth->expects($this->any())
            ->method('__invoke')
            ->with(null)
            ->willReturn($auth)
        ;
        $this->defaultListener = $this->createMock(DefaultListener::class);
        $plugins = $this->createMock(PluginManager::class);

        $plugins->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['Auth',null,$auth],
                ['layout',null,$layout],
                ['forward',null,$forward],
                ['config',null,$config]
            ])
        ;
        $this->pluginsMock = $plugins;
        $this->moduleManager = $this->createMock(ModuleManager::class);
        $this->controller = new IndexController(
            $this->defaultListener,
            $this->config,
            $this->moduleManager
        );
        $this->init('index');
        $this->controller->setEvent($this->event);
        $this->controller->setPluginManager($this->pluginsMock);
    }

    /**
     * Test if indexAction redirect to startpage
     * if user not loggedin
     */
    public function testIndexRedirectToStartPageWhenNotLoggedIn()
    {
        $auth = $this->createMock(Auth::class);

        $auth->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false)
        ;

        $this->config['view_manager']['template_map']['startpage'] = ['some.template'];

        $viewModel = $this->createMock(ViewModel::class);
        $layout = $this->createMock(Layout::class);
        $layout->expects($this->once())
            ->method('__invoke')
            ->willReturn($viewModel)
        ;
        $viewModel->expects($this->once())
            ->method('setTerminal')
            ->with(true)
            ->willReturn($viewModel)
        ;
        $viewModel->expects($this->once())
            ->method('setTemplate')
            ->with('startpage')
            ->willReturn($viewModel)
        ;
        $this->setupTarget($auth,$layout);
        $request = new Request();
        $this->controller->dispatch($request);
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
    }

    public function testIndexForwardToDashboard()
    {
        $auth = $this->createMock(Auth::class);
        $auth->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true)
        ;
        $this->config['dashboard'] = [];

        $forward = $this->createMock(Forward::class);
        $forward->expects($this->any())
            ->method('dispatch')
            ->with('Core\\Controller\\Index',['action'=>'dashboard'])
        ;
        $request = new Request();
        $this->setupTarget($auth,null,$forward);
        $this->controller->dispatch($request);
    }

    public function testDashboardAction()
    {
        $auth = $this->createMock(Auth::class);
        $auth->expects($this->any())
            ->method('isLoggedIn')
            ->willReturn(true)
        ;

        $modules = [
            'Disabled' => [
                'enabled' => false,
            ],
            'Jobs' => [
                'enabled' => true,
                'widgets' => [
                    'recentJobs' => [
                        'controller' => 'Jobs/Index',
                        'params' => ['type' => 'recent'],
                    ],
                    'scriptTest' => [
                        'script' => 'some-script'
                    ],
                    'contentTest' => [
                        'content' => 'some-content'
                    ]
                ],
            ]
        ];
        $config = $this->createMock(Config::class);
        $config->expects($this->any())
            ->method('__invoke')
            ->with('dashboard',[0])
            ->willReturn($modules)
        ;

        $viewModel = new ViewModel();
        $viewModel->setTemplate('some-template');
        $response = $this->createMock(Response::class);
        // dashboardAction should dispatch controller type widget
        $forward = $this->createMock(Forward::class);
        $forward->expects($this->any())
            ->method('dispatch')
            ->with(
                'Jobs/Index',
                ['action' => 'dashboard','type'=>'recent']
            )
            ->willReturnOnConsecutiveCalls(null,$viewModel)
        ;

        $this->init('Core\Controller\Index','dashboard');
        $this->setupTarget($auth,null,$forward,$config);
        $this->moduleManager->expects($this->any())
            ->method('getLoadedModules')
            ->willReturn(['test'])
        ;

        $target = $this->getMockBuilder(IndexController::class)
            ->setConstructorArgs([$this->defaultListener,$this->config,$this->moduleManager])
            ->setMethods(['getResponse'])
            ->getMock()
        ;
        $target->setPluginManager($this->pluginsMock);
        $target->expects($this->any())
            ->method('getResponse')
            ->willReturn($response)
        ;


        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturnOnConsecutiveCalls(400,200)
        ;

        // first widget response: inform when error loading widget
        $viewModel = $target->dashboardAction();
        $this->assertEquals(
            'Error loading widget.',
            $viewModel->getChildren()[0]->getVariable('content')
        );

        // second widget response: render widget
        $outputModel = $target->dashboardAction();
        $childModel = $outputModel->getChildren()[0];
        $this->assertEquals(
            'core/index/dashboard',
            $outputModel->getTemplate()
        );
        $this->assertEquals(
            'core/index/dashboard-widget.phtml',
            $childModel->getTemplate()
        );
    }

    public function testErrorAction()
    {
        $auth = $this->createMock(Auth::class);
        $this->setupTarget($auth);
        $viewModel = $this->controller->errorAction();

        $this->assertEquals(
            'error/index',
            $viewModel->getTemplate()
        );
        $this->assertContains(
            'An unexpected error had occured.',
            $viewModel->getVariable('message')
        );
    }
}
