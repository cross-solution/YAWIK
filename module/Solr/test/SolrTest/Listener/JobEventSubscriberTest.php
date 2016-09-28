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
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
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
        $this->assertContains(Events::postUpdate, $subscribedEvents);
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
     * @param bool $active
     * @covers ::preUpdate()
     * @dataProvider boolData()
     */
    public function testPreUpdateWithChangedStatus($active)
    {
        $job = $this->getMockBuilder(Job::class)
            ->getMock();
        $job->expects($this->once())
            ->method('isActive')
            ->willReturn($active);
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
        $this->assertAttributeContains($job, $active ? 'add' : 'delete', $this->subscriber);
    }
    
    /**
     * @covers ::postUpdate()
     */
    public function testPostUpdateWithNoJobsToProcess()
    {
        $subscriber = $this->getMockBuilder(JobEventSubscriber::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSolrClient'])
            ->getMock();
        $subscriber->expects($this->never())
            ->method('getSolrClient');
        
        $event = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $subscriber->postUpdate($event);
    }
    
    /**
     * @covers ::postUpdate()
     * @covers ::getSolrClient()
     * @dataProvider boolData()
     */
    public function testPostUpdateWithJobsToProcess($active)
    {
        $job = $this->getMockBuilder(Job::class)->getMock();
        $job->expects($this->once())
            ->method('isActive')
            ->willReturn($active);
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
        
        if ($active) {
            $document = new SolrInputDocument();
            $this->entityToDocumentFilter->expects($this->once())
                ->method('filter')
                ->with($this->identicalTo($job))
                ->willReturn($document);
            
            $this->client->expects($this->once())
                ->method('addDocument')
                ->with($this->identicalTo($document));
        } else {
            $ids = [1, 2, 3];
            $this->entityToDocumentFilter->expects($this->once())
                ->method('getDocumentIds')
                ->with($this->identicalTo($job))
                ->willReturn($ids);
            
            $this->client->expects($this->once())
                ->method('deleteByIds')
                ->with($this->identicalTo($ids));
        }
        
        $event = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->subscriber->postUpdate($event);
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
     * @covers ::postFlush()
     */
    public function testPostFlushWithJobsToProcess()
    {
        $job = $this->getMockBuilder(Job::class)->getMock();
        $job->expects($this->once())
            ->method('isActive')
            ->willReturn(true);
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
        
        $this->client->expects($this->once())
            ->method('commit');
        $this->client->expects($this->once())
            ->method('optimize');
        
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
    public function boolData()
    {
        return [
            [false],
            [true],
        ];
    }
}
