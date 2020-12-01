<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\ImageFileCache;

use Core\Service\FileManager;
use Organizations\Entity\OrganizationImage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Organizations\ImageFileCache\ApplicationListener;
use Organizations\ImageFileCache\Manager as CacheManager;
use Organizations\Repository\OrganizationImage as ImageRepository;
use Organizations\Entity\OrganizationImageMetadata as ImageMetadata;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Application;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\Response;
use Laminas\Http\Response\Stream;
use Laminas\Http\Headers;

/**
 * @coversDefaultClass \Organizations\ImageFileCache\ApplicationListener
 */
class ApplicationListenerTest extends TestCase
{

    /**
     * @var ApplicationListener
     */
    protected $listener;

    /**
     * @var CacheManager|MockObject
     */
    protected $cacheManager;

    /**
     * @var ImageRepository|MockObject
     */
    protected $repository;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Request|MockObject
     */
    protected $request;

    /**
     * @var MockObject|\Core\Service\FileManager
     */
    protected $fileManager;

    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder(ImageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = new MvcEvent();
        $this->event->setRequest($this->request);

        $this->fileManager = $this->createMock(FileManager::class);

        $this->listener = new ApplicationListener($this->cacheManager,$this->fileManager);
    }

    /**
     * @param string $error
     * @param bool $interested
     * @covers ::__construct
     * @covers ::onDispatchError
     * @dataProvider dataOnDispatchErrorIsInterestedInRightError
     */
    public function testOnDispatchErrorIsInterestedInRightError($error, $interested)
    {
        $this->event->setError($error);
        $this->request->expects($interested ? $this->once() : $this->never())
            ->method('getRequestUri');

        $this->listener->onDispatchError($this->event);
    }

    /**
     * @return array
     */
    public function dataOnDispatchErrorIsInterestedInRightError()
    {
        return [
            [Application::ERROR_CONTROLLER_CANNOT_DISPATCH, false],
            [Application::ERROR_CONTROLLER_NOT_FOUND, false],
            [Application::ERROR_CONTROLLER_INVALID, false],
            [Application::ERROR_EXCEPTION, false],
            [Application::ERROR_ROUTER_NO_MATCH, true],
            [Application::ERROR_MIDDLEWARE_CANNOT_DISPATCH, false]
        ];
    }

    /**
     * @param string $id
     * @covers ::onDispatchError
     * @dataProvider dataOnDispatchErrorUriMatch
     */
    public function testOnDispatchErrorUriMatch($id)
    {
        $uri = '/some/uri';
        $this->event->setError(Application::ERROR_ROUTER_NO_MATCH);

        $this->request->expects($this->once())
            ->method('getRequestUri')
            ->willReturn($uri);

        $this->cacheManager->expects($this->once())
            ->method('matchUri')
            ->with($this->equalTo($uri))
            ->willReturn($id);

        if ($id) {
            $this->fileManager->expects($this->once())
                ->method('findByID')
                ->with(OrganizationImage::class, $this->equalTo($id));
        } else {
            $this->fileManager->expects($this->never())
                ->method('findByID');
        }

        $this->listener->onDispatchError($this->event);
    }

    /**
     * @return array
     */
    public function dataOnDispatchErrorUriMatch()
    {
        return [
            [null],
            ['someId']
        ];
    }

    /**
     * @covers ::onDispatchError
     */
    public function testOnDispatchErrorStripBaseUrl()
    {
        $baseUrl = '/base';
        $path = '/some/uri';
        $uri = $baseUrl . $path;
        $this->event->setError(Application::ERROR_ROUTER_NO_MATCH);

        $this->request->expects($this->once())
            ->method('getRequestUri')
            ->willReturn($uri);

        $this->request->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($baseUrl);

        $this->cacheManager->expects($this->once())
            ->method('matchUri')
            ->with($this->equalTo($path));

        $this->listener->onDispatchError($this->event);
    }

    /**
     * @covers ::onDispatchError
     */
    public function testOnDispatchErrorNonExistentImage()
    {
        $id = 'someId';
        $this->event->setError(Application::ERROR_ROUTER_NO_MATCH);

        $this->cacheManager->expects($this->once())
            ->method('matchUri')
            ->willReturn($id);

        $this->fileManager->expects($this->once())
            ->method('findByID')
            ->with(OrganizationImage::class, $this->equalTo($id));

        $this->cacheManager->expects($this->never())
            ->method('store');

        $this->listener->onDispatchError($this->event);
    }

    /**
     * @covers ::onDispatchError
     */
    public function testOnDispatchErrorStoreAndStreamImage()
    {
        $id = 'someId';
        $resource = 'someResource';
        $this->event->setError(Application::ERROR_ROUTER_NO_MATCH);

        $metadata = $this->getMockBuilder(ImageMetadata::class)
            ->setMethods(['getLength', 'getContentType', 'getId'])
            ->getMock();
        $metadata->expects($this->any())
            ->method('getContentType')
            ->willReturn('image/jpeg');
        $metadata->expects($this->any())
            ->method('getId')
            ->willReturn($id);


        $image = $this->createMock(OrganizationImage::class);
        $image->expects($this->once())
            ->method('getMetadata')
            ->willReturn($metadata);
        $image->expects($this->any())
            ->method('getLength')
            ->willReturn(1024);
        $image->expects($this->any())
            ->method('getName')
            ->willReturn('image.jpg');

        $this->fileManager->expects($this->once())
            ->method('findByID')
            ->with(OrganizationImage::class, $id)
            ->willReturn($image);

        $this->fileManager->expects($this->once())
            ->method('getContents')
            ->with($image)
            ->willReturn('contents');

        $this->fileManager->expects($this->once())
            ->method('getStream')
            ->with($image)
            ->willReturn($resource);

        $this->cacheManager->expects($this->once())
            ->method('matchUri')
            ->willReturn($id);

        $this->cacheManager->expects($this->once())
            ->method('store')
            ->with($image, 'contents');

        $this->listener->onDispatchError($this->event);

        $response = $this->event->getResponse();
        $this->assertInstanceOf(Stream::class, $response);
        $this->assertEquals(Response::STATUS_CODE_200, $response->getStatusCode());
        $this->assertEquals($image->getName(), $response->getStreamName());
        $this->assertEquals($resource, $response->getStream());

        $headers = $response->getHeaders();
        $this->assertInstanceOf(Headers::class, $headers);
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals($metadata->getContentType(), $headers->get('Content-Type')->getFieldValue());
        $this->assertTrue($headers->has('Content-Length'));
        $this->assertEquals($image->getLength(), $headers->get('Content-Length')->getFieldValue());
    }
}
