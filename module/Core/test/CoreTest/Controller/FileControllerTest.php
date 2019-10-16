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

use Acl\Controller\Plugin\Acl;
use Core\Controller\FileController;
use Core\EventManager\EventManager;
use Core\Repository\RepositoryService;
use Doctrine\Common\Persistence\ObjectRepository;
use Interop\Container\ContainerInterface;
use Organizations\Entity\OrganizationImage;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ResponseCollection;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;
use Zend\View\Model\JsonModel;

/**
 * Class FileControllerTest
 *
 * @package CoreTest\Controller
 * @covers \Core\Controller\FileController
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 */
class FileControllerTest extends AbstractControllerTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repositoriesMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $pluginManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $paramsPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $aclPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;

    protected function setUp(): void
    {
        $this->init('file');
        $this->repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->events = $this->createMock(EventManager::class);
        $this->controller = new FileController(
            $this->repositoriesMock,
            $this->events
        );
        $this->controller->setEvent($this->event);

        $this->paramsPlugin = $this->createMock(Params::class);
        $this->aclPlugin = $this->createMock(Acl::class);
        $plugins = $this->createMock(PluginManager::class);
        $plugins->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['acl',null,$this->aclPlugin],
                ['params',null,$this->paramsPlugin]
            ])
        ;

        $this->paramsPlugin->expects($this->any())
            ->method('__invoke')
            ->willReturnMap([
                [null,null,$this->paramsPlugin],
                ['filestore',null,'store.entity'],
                ['fileId',0,'dir/file.ext']
            ])
        ;
        $this->paramsPlugin->expects($this->any())
            ->method('fromQuery')
            ->with('do')
            ->willReturn('delete')
        ;
        $this->controller->setPluginManager($plugins);
    }

    public function testIndexWithInvalidFileReturn404()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $repo = $this->createMock(ObjectRepository::class);

        $this->repositoriesMock->expects($this->any())
            ->method('get')
            ->with('store/entity')
            ->willReturn($repo)
        ;

        $repo->expects($this->once())
            ->method('find')
            ->with('dir/file')
            ->willReturn(null)
        ;

        $response = $this->controller->dispatch($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }

    public function testIndexThrowExceptionReturn404()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $e = new \Exception('test exception');
        $this->repositoriesMock->expects($this->any())
            ->method('get')
            ->with('store/entity')
            ->willThrowException($e)
        ;

        $response = $this->controller->dispatch($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
        $this->assertEquals(
            $this->event->getParam('exception'),
            $e
        );
    }

    public function testIndexProcessOrganizationImage()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);

        $repo = $this->createMock(ObjectRepository::class);
        $file = $this->createMock(OrganizationImage::class);
        $file->expects($this->once())
            ->method('getType')
            ->willReturn('type')
        ;
        $file->expects($this->once())
            ->method('getLength')
            ->willReturn('length')
        ;

        $this->repositoriesMock->expects($this->once())
            ->method('get')
            ->with('store/entity')
            ->willReturn($repo)
        ;

        $repo->expects($this->once())
            ->method('find')
            ->with('dir/file')
            ->willReturn($file)
        ;


        $resource = fopen(__FILE__, 'r');
        $file->expects($this->once())
            ->method('getResource')
            ->willReturn($resource)
        ;

        ob_start();
        $this->controller->dispatch($request);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertStringEqualsFile(__FILE__, $output);
        $this->assertEquals(
            'type',
            $this->getResponseHeader('Content-Type')->getFieldValue()
        );
        $this->assertEquals(
            'length',
            $this->getResponseHeader('Content-Length')->getFieldValue()
        );
    }

    public function testDeleteWithInvalidFileReturn500()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $headers = $request->getHeaders();
        $headers
            ->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest')
        ;

        $repo = $this->createMock(ObjectRepository::class);

        $this->repositoriesMock->expects($this->any())
            ->method('get')
            ->with('store/entity')
            ->willReturn($repo)
        ;

        $repo->expects($this->once())
            ->method('find')
            ->with('dir/file')
            ->willReturn(null)
        ;

        $output = $this->controller->dispatch($request);
        $this->assertInstanceOf(JsonModel::class, $output);
        $this->assertEquals(
            '{"result":false,"message":"File not found."}',
            $output->serialize()
        );
    }

    public function testDeleteAction()
    {
        $request = new Request();
        $headers = $request->getHeaders();
        $headers
            ->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest')
        ;

        $repo = $this->createMock(ObjectRepository::class);
        $file = $this->createMock(OrganizationImage::class);

        $this->repositoriesMock->expects($this->once())
            ->method('get')
            ->with('store/entity')
            ->willReturn($repo)
        ;

        $repo->expects($this->once())
            ->method('find')
            ->with('dir/file')
            ->willReturn($file)
        ;

        $event = $this->createMock(EventInterface::class);
        $eventResponses = $this->createMock(ResponseCollection::class);
        $this->events->expects($this->once())
            ->method('getEvent')
            ->willReturn($event)
        ;
        $this->events->expects($this->once())
            ->method('triggerEventUntil')
            ->with($this->anything())
            ->willReturn($eventResponses)
        ;
        $eventResponses->expects($this->once())
            ->method('last')
            ->willReturn(false);

        $this->repositoriesMock->expects($this->once())
            ->method('remove')
            ->with($file)
        ;

        /* @var \Zend\View\Model\JsonModel $output */
        $output = $this->controller->dispatch($request);
        $this->assertInstanceOf(JsonModel::class, $output);
        $this->assertEquals('{"result":true}', $output->serialize());
    }
}
