<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Listener;


use Core\Options\ModuleOptions;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
use Jobs\Entity\StatusInterface;
use Solr\Bridge\Manager;
use Solr\Filter\EntityToDocument\Job as EntityToDocumentFilter;
use Solr\Listener\JobEventSubscriber;
use Zend\ServiceManager\ServiceLocatorInterface;;
use SolrInputDocument;

/**
 * Test for Solr\Listener\JobEventSubscriber
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @requires extension solr
 * @package SolrTest\Listener
 * @coversDefaultClass \Solr\Listener\JobEventSubscriber
 */
class JobEventSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobEventSubscriber
     */
    protected $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityToDocumentFilter;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->setMethods(['getJobsPath'])
            ->getMock();
        $options->method('getJobsPath')
            ->willReturn('/some/path');
        
        $this->client = $this->getMockBuilder(\SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager->method('getClient')
            ->willReturn($this->client);
        $this->manager->method('getOptions')
            ->willReturn($options);
        
        $this->entityToDocumentFilter = $this->getMockBuilder(EntityToDocumentFilter::class)
            ->getMock();

        $this->subscriber = new JobEventSubscriber($this->manager, $this->entityToDocumentFilter);
    }

    /**
     * @covers ::__construct()
     * @covers ::getSubscribedEvents()
     */
    public function testShouldSubscribeToDoctrineEvent()
    {
        $subscribedEvents = $this->subscriber->getSubscribedEvents();

        $this->assertContains(Events::preUpdate, $subscribedEvents);
        $this->assertContains(Events::postFlush, $subscribedEvents);
    }

    /**
     * @covers ::preUpdate()
     */
    public function testPreUpdateShouldNotProcessNonJobDocument()
    {
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getDocument');
        $event->expects($this->never())
            ->method('hasChangedField');
        
        $this->subscriber->preUpdate($event);
    }

    /**
     * @covers ::preUpdate()
     */
    public function testPreUpdateShouldNotProcessDocumentWithUnchangedStatus()
    {
        $job = $this->getMockBuilder(Job::class)
            ->getMock();
        $job->expects($this->never())
            ->method('isActive');
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn($job);
        $event->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('status'))
            ->willReturn(false);

        $this->subscriber->preUpdate($event);
    }

    /**
     * @param string $status
     * @param bool $shouldBeAdded
     * @param bool $shouldBeDeleted
     * @covers ::preUpdate()
     * @dataProvider jobStateData()
     */
    public function testPreUpdateWithChangedStatus($status, $shouldBeAdded, $shouldBeDeleted)
    {
        $job = new Job();
        $job->setStatus($status);
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn($job);
        $event->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('status'))
            ->willReturn(true);

        $this->subscriber->preUpdate($event);
        
        if ($shouldBeAdded) {
            $this->assertAttributeContains($job, 'add', $this->subscriber);
        } else {
            $this->assertAttributeNotContains($job, 'add', $this->subscriber);
        }
        
        if ($shouldBeDeleted) {
            $this->assertAttributeContains($job, 'delete', $this->subscriber);
        } else {
            $this->assertAttributeNotContains($job, 'delete', $this->subscriber);
        }
    }
    
    /**
     * @covers ::postFlush()
     */
    public function testPostFlushWithNoJobsToProcess()
    {
        $subscriber = $this->getMockBuilder(JobEventSubscriber::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSolrClient'])
            ->getMock();
        $subscriber->expects($this->never())
            ->method('getSolrClient');
        
        $event = $this->getMockBuilder(PostFlushEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $subscriber->postFlush($event);
    }
    
    /**
     * @param string $status
     * @param bool $shouldBeAdded
     * @param bool $shouldBeDeleted
     * @covers ::postFlush()
     * @covers ::getSolrClient()
     * @dataProvider jobStateData()
     */
    public function testPostFlushWithJobsToProcess($status, $shouldBeAdded, $shouldBeDeleted)
    {
        $job = new Job();
        $job->setStatus($status);
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getDocument')
            ->willReturn($job);
        $event->expects($this->once())
            ->method('hasChangedField')
            ->with($this->equalTo('status'))
            ->willReturn(true);
        
        $this->subscriber->preUpdate($event);
        
        if ($shouldBeAdded) {
            $document = new SolrInputDocument();
            $this->entityToDocumentFilter->expects($this->once())
                ->method('filter')
                ->with($this->identicalTo($job))
                ->willReturn($document);
            
            $this->client->expects($this->once())
                ->method('addDocument')
                ->with($this->identicalTo($document));
        }
        
        if ($shouldBeDeleted) {
            $ids = [1, 2, 3];
            $this->entityToDocumentFilter->expects($this->once())
                ->method('getDocumentIds')
                ->with($this->identicalTo($job))
                ->willReturn($ids);
            
            $this->client->expects($this->once())
                ->method('deleteByIds')
                ->with($this->identicalTo($ids));
        }
        
        if ($shouldBeAdded || $shouldBeDeleted) {
            $this->client->expects($this->once())
                ->method('commit');
            $this->client->expects($this->once())
                ->method('optimize');
        }
        
        $event = $this->getMockBuilder(PostFlushEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->subscriber->postFlush($event);
    }
    
    /**
     * @covers ::factory()
     */
    public function testFactory()
    {
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Solr/Manager'))
            ->willReturn($this->manager);
        
        $this->assertInstanceOf(JobEventSubscriber::class, JobEventSubscriber::factory($serviceLocator));
    }
    
    /**
     * @return array
     */
    public function jobStateData()
    {
        return [
            [StatusInterface::ACTIVE, true, false],
            [StatusInterface::CREATED, false, false],
            [StatusInterface::EXPIRED, false, true],
            [StatusInterface::INACTIVE, false, true],
            [StatusInterface::PUBLISH, false, false],
            [StatusInterface::REJECTED, false, false]
        ];
    }
}
