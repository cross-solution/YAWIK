<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
namespace CoreTest\Repository\DoctrineMongoODM\Event;

use PHPUnit\Framework\TestCase;

use Core\Repository\DoctrineMongoODM\Event\RepositoryEventsSubscriber;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Core\Entity\AttachableEntityInterface;
use Core\Entity\AttachableEntityManager;
use Core\Repository\RepositoryService;
use Core\Repository\DoctrineMongoODM\Event\EventArgs;
use Core\Repository\RepositoryInterface;
use Core\Entity\EntityInterface;

/**
 * @coversDefaultClass \Core\Repository\DoctrineMongoODM\Event\RepositoryEventsSubscriber
 */
class RepositoryEventsSubscriberTest extends TestCase
{
    /**
     * @var RepositoryEventsSubscriber
     */
    protected $subscriber;
    
    /**
     * @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $services;
    
    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->services = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $this->services->method('get')
            ->will($this->returnValueMap([
                ['repositories', $repositories]
            ]));
        
        $this->subscriber = new RepositoryEventsSubscriber($this->services);
    }
    
    /**
     * @covers ::factory()
     */
    public function testFactory()
    {
        $this->assertInstanceOf(RepositoryEventsSubscriber::class, RepositoryEventsSubscriber::factory($this->services));
    }
    
    /**
     * @covers ::__construct()
     * @covers ::getSubscribedEvents()
     */
    public function testGetSubscribedEvents()
    {
        $subscribedEvents = $this->subscriber->getSubscribedEvents();
        $this->assertContains(RepositoryEventsSubscriber::postConstruct, $subscribedEvents);
        $this->assertContains(RepositoryEventsSubscriber::postCreate, $subscribedEvents);
        $this->assertContains(Events::postLoad, $subscribedEvents);
    }
    
    /**
     * @covers ::postRepositoryConstruct()
     */
    public function testPostRepositoryConstruct()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)
            ->getMock();
        $documentName = get_class($entity);
        
        $repository = $this->getMockBuilder(RepositoryInterface::class)
            ->setMethods(['getDocumentName', 'setEntityPrototype', 'init', 'create'])
            ->getMock();
        $repository->expects($this->once())
            ->method('getDocumentName')
            ->willReturn($documentName);
        $repository->expects($this->once())
            ->method('setEntityPrototype')
            ->with($this->callback(function ($entity) use ($documentName) {
                return $entity instanceof $documentName;
            }));
        $repository->expects($this->once())
            ->method('init')
            ->with($this->identicalTo($this->services));
        
        $eventArgs = $this->getMockBuilder(EventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventArgs->expects($this->once())
            ->method('get')
            ->with($this->equalTo('repository'))
            ->willReturn($repository);
        
        $this->subscriber->postRepositoryConstruct($eventArgs);
    }
    
    /**
     * @covers ::postRepositoryCreate()
     * @covers ::injectAttachableEntityManager()
     */
    public function testPostRepositoryCreate()
    {
        $attachableEntity = $this->getMockBuilder(AttachableEntityInterface::class)
            ->getMock();
        $attachableEntity->expects($this->once())
            ->method('setAttachableEntityManager')
            ->with($this->callback(function ($manager) {
                return $manager instanceof AttachableEntityManager;
            }));
        
        $eventArgs = $this->getMockBuilder(EventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventArgs->expects($this->once())
            ->method('get')
            ->with($this->equalTo('entity'))
            ->willReturn($attachableEntity);
        
        $this->subscriber->postRepositoryCreate($eventArgs);
    }
    
    /**
     * @covers ::postLoad()
     * @covers ::injectAttachableEntityManager()
     */
    public function testPostLoad()
    {
        $attachableEntity = $this->getMockBuilder(AttachableEntityInterface::class)
            ->getMock();
        $attachableEntity->expects($this->once())
            ->method('setAttachableEntityManager')
            ->with($this->callback(function ($manager) {
                return $manager instanceof AttachableEntityManager;
            }));
        
        $eventArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventArgs->expects($this->once())
            ->method('getDocument')
            ->willReturn($attachableEntity);
        
        $this->subscriber->postLoad($eventArgs);
    }
}
