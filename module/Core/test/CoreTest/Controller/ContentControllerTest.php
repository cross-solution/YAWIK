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

use Core\Controller\ContentController;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;

/**
 * Class ContentControllerTest
 *
 * @package CoreTest\Controller
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 */
class ContentControllerTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    protected $target = ContentController::class;

    protected $inheritance = [AbstractActionController::class];

    protected function setUp(): void
    {
        $this->target = new ContentController();
    }

    public function testIndexAction()
    {
        $pluginManager = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $request = $this->createMock(Request::class);

        $target = $this->getMockBuilder(ContentController::class)
            ->setMethods(['getRequest','getPluginManager'])
            ->getMock()
        ;

        $pluginManager->expects($this->exactly(2))
            ->method('get')
            ->with('params')
            ->willReturn($params)
        ;
        $target->expects($this->exactly(2))
            ->method('getPluginManager')
            ->willReturn($pluginManager)
        ;
        $target->expects($this->exactly(2))
            ->method('getRequest')
            ->willReturn($request)
        ;

        $request->expects($this->exactly(2))
            ->method('isXmlHttpRequest')
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $params->expects($this->exactly(2))
            ->method('__invoke')
            ->with('view')
            ->willReturn('some_view')
        ;


        $viewModel = $target->indexAction();
        $this->assertEquals(
            'content/some_view',
            $viewModel->getTemplate()
        );
        $this->assertFalse($viewModel->terminate());

        $viewModel = $target->indexAction();
        $this->assertEquals(
            'content/some_view',
            $viewModel->getTemplate()
        );
        $this->assertTrue($viewModel->terminate());
    }

    public function testModalAction()
    {
        $pluginManager = $this->createMock(PluginManager::class);
        $params = $this->createMock(Params::class);
        $request = $this->createMock(Request::class);

        $target = $this->getMockBuilder(ContentController::class)
            ->setMethods(['getRequest','getPluginManager'])
            ->getMock()
        ;

        $pluginManager->expects($this->exactly(2))
            ->method('get')
            ->with('params')
            ->willReturn($params)
        ;
        $target->expects($this->exactly(2))
            ->method('getPluginManager')
            ->willReturn($pluginManager)
        ;
        $target->expects($this->exactly(2))
            ->method('getRequest')
            ->willReturn($request)
        ;

        $request->expects($this->exactly(2))
            ->method('isXmlHttpRequest')
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $params->expects($this->exactly(2))
            ->method('__invoke')
            ->with('view')
            ->willReturn('some_view')
        ;


        $viewModel = $target->modalAction();
        $this->assertEquals(
            'some_view',
            $viewModel->getTemplate()
        );
        $this->assertFalse($viewModel->terminate());

        $viewModel = $target->modalAction();
        $this->assertEquals(
            'some_view',
            $viewModel->getTemplate()
        );
        $this->assertTrue($viewModel->terminate());
    }
}
