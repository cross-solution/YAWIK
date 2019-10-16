<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\ImageFileCache;

use PHPUnit\Framework\TestCase;

use Organizations\ImageFileCache\ApplicationListener;
use Organizations\ImageFileCache\Manager;
use Organizations\Repository\OrganizationImage as ImageRepository;
use Organizations\Entity\OrganizationImage as ImageEntity;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Http\Response\Stream;
use Zend\Http\Headers;

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
     * @var Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var ImageRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Request|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;
    
    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->manager = $this->getMockBuilder(Manager::class)
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
        
        $this->listener = new ApplicationListener($this->manager, $this->repository);
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
        
        $this->manager->expects($this->once())
            ->method('matchUri')
            ->with($this->equalTo($uri))
            ->willReturn($id);
        
        if ($id) {
            $this->repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo($id));
        } else {
            $this->repository->expects($this->never())
                ->method('find');
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
        
        $this->manager->expects($this->once())
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
        
        $this->manager->expects($this->once())
            ->method('matchUri')
            ->willReturn($id);
        
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id));
        
        $this->manager->expects($this->never())
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
        
        $image = $this->getMockBuilder(ImageEntity::class)
            ->setMethods(['getLength', 'getResource'])
            ->getMock();
        $image->setId($id);
        $image->setType('image/jpeg');
        $image->setName('image.jpg');
        $image->method('getLength')
            ->willReturn(1024);
        $image->method('getResource')
            ->willReturn($resource);
        
        $this->manager->expects($this->once())
            ->method('matchUri')
            ->willReturn($id);
        
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id))
            ->willReturn($image);
        
        $this->manager->expects($this->once())
            ->method('store')
            ->with($this->identicalTo($image));
        
        $this->listener->onDispatchError($this->event);
        
        $response = $this->event->getResponse();
        $this->assertInstanceOf(Stream::class, $response);
        $this->assertEquals(Response::STATUS_CODE_200, $response->getStatusCode());
        $this->assertEquals($image->getName(), $response->getStreamName());
        $this->assertEquals($image->getResource(), $response->getStream());
        
        $headers = $response->getHeaders();
        $this->assertInstanceOf(Headers::class, $headers);
        $this->assertTrue($headers->has('Content-Type'));
        $this->assertEquals($image->getType(), $headers->get('Content-Type')->getFieldValue());
        $this->assertTrue($headers->has('Content-Length'));
        $this->assertEquals($image->getLength(), $headers->get('Content-Length')->getFieldValue());
    }
}
