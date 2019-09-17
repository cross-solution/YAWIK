<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller;

use PHPUnit\Framework\TestCase;

use Auth\Controller\Plugin\Auth;
use Zend\Http\Response;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\Plugin\Forward;
use Zend\Mvc\Controller\Plugin\Layout;
use Zend\Mvc\Controller\PluginManager;
use Core\Controller\IndexController;
use Core\Listener\DefaultListener;
use Core\Controller\Plugin\Config;
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Class IndexControllerTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @covers \Core\Controller\IndexController
 * @package CoreTest\Controller
 */
class IndexControllerTest extends AbstractControllerTestCase
{
    private $config;

    private $defaultListener;

    private $moduleManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $pluginsMock;

    /**
     * @return IndexController
     */
    public function setupTarget($auth=null, $layout=null, $forward=null, $config=null)
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
            $this->moduleManager,
            $this->config
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
        $this->setupTarget($auth, $layout);
        $request = new Request();
        $this->controller->dispatch($request);
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
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
            ->with('dashboard', [0])
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
            ->willReturnOnConsecutiveCalls(null, $viewModel)
        ;

        $this->init('Core\Controller\Index', 'dashboard');
        $this->setupTarget($auth, null, $forward, $config);
        $this->moduleManager->expects($this->any())
            ->method('getLoadedModules')
            ->willReturn(['test'])
        ;

        $target = $this->getMockBuilder(IndexController::class)
            ->setConstructorArgs([$this->moduleManager,$this->config])
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
            ->willReturnOnConsecutiveCalls(400, 200)
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
