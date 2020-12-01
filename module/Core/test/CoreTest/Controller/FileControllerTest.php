<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Controller;

use Core\Entity\FileMetadataInterface;
use Core\Service\FileManager;
use Organizations\Entity\OrganizationImageMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Acl\Controller\Plugin\Acl;
use Core\Controller\FileController;
use Core\EventManager\EventManager;
use Doctrine\Persistence\ObjectRepository;
use Organizations\Entity\OrganizationImage;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\ResponseCollection;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\View\Model\JsonModel;

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
     * @var MockObject|ObjectRepository
     */
    private $repositoriesMock;

    /**
     * @var MockObject|Params
     */
    private $paramsPlugin;

    /**
     * @var MockObject|Acl
     */
    private $aclPlugin;

    /**
     * @var MockObject|EventManager
     */
    private $events;

    /**
     * @var FileManager|MockObject
     */
    private $fileManager;

    protected function setUp(): void
    {
        $this->init('file');
        $this->repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->fileManager = $this->createMock(FileManager::class);
        $this->events = $this->createMock(EventManager::class);
        $this->controller = new FileController(
            $this->fileManager,
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

        $this->fileManager->expects($this->any())
            ->method('findByID')
            ->with('store\Entity\entity','dir/file')
            ->willReturn(null)
        ;

        $response = $this->controller->dispatch($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }

    public function testIndexProcessOrganizationImage()
    {
        $request = new Request();
        $file = $this->createMock(OrganizationImage::class);
        $metadata = $this->getMockBuilder(OrganizationImageMetadata::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContentType', 'getLength'])
            ->getMock()
        ;

        $request->setMethod(Request::METHOD_GET);

        $file->expects($this->once())
            ->method('getMetadata')
            ->willReturn($metadata);
        $metadata->expects($this->once())
            ->method('getContentType')
            ->willReturn('type')
        ;
        $file->expects($this->once())
            ->method('getLength')
            ->willReturn(1024)
        ;

        $this->fileManager->expects($this->once())
            ->method('findByID')
            ->with('store\Entity\entity')
            ->willReturn($file)
        ;

        $resource = fopen(__FILE__, 'r');
        $this->fileManager->expects($this->once())
            ->method('getStream')
            ->with($file)
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
            1024,
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

        $this->fileManager->expects($this->any())
            ->method('findByID')
            ->with('store\Entity\entity', 'dir/file')
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

        $file = $this->createMock(OrganizationImage::class);

        $this->fileManager->expects($this->once())
            ->method('findByID')
            ->with('store\Entity\entity', 'dir/file')
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


        $this->fileManager->expects($this->once())
            ->method('remove')
            ->with($file)
        ;

        /* @var \Laminas\View\Model\JsonModel $output */
        $output = $this->controller->dispatch($request);
        $this->assertInstanceOf(JsonModel::class, $output);
        $this->assertEquals('{"result":true}', $output->serialize());
    }
}
