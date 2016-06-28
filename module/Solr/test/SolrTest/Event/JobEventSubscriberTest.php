<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Event;


use CoreTestUtils\TestCase\FunctionalTestCase;
use Cv\Entity\Cv;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
use Solr\Event\JobEventSubscriber;

class JobEventSubscriberTest extends FunctionalTestCase
{
    /**
     * @var JobEventSubscriber
     */
    protected $target;

    /**
     * @var \SolrClient
     */
    protected $clientMock;

    public function setUp()
    {
        parent::setUp();
        $sl = $this->getApplicationServiceLocator();

        $clientMock = $this->getMockBuilder(\SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sl->setService('Solr/Client', $clientMock);
        $this->target = new JobEventSubscriber(
            $clientMock
        );
        $this->clientMock = $clientMock;
    }

    public function testShouldSubscribeToDoctrineEvent()
    {
        $subscribedEvents = $this->target->getSubscribedEvents();

        $this->assertContains(Events::postUpdate, $subscribedEvents);
        $this->assertContains(Events::postPersist, $subscribedEvents);
    }

    public function testPostPersistShouldNotProcessNonJobDocument()
    {
        $cv = new Cv();
        $mock = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getDocument')
            ->willReturn($cv);
        $this->clientMock
            ->expects($this->never())
            ->method('addDocument');
        $this->target->postPersist($mock);
    }

    public function testShouldProcessOnPersistEvent()
    {
        $job = new Job();
        $mock = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getDocument')
            ->willReturn($job);
        $this->clientMock
            ->expects($this->once())
            ->method('addDocument')
            ->with($this->isInstanceOf(\SolrInputDocument::class));
        $this->clientMock
            ->expects($this->once())
            ->method('commit');
        $this->clientMock
            ->expects($this->once())
            ->method('optimize');
        $this->target->postPersist($mock);
    }

    public function testPostUpdateShouldNotProcessNonJobDocument()
    {
        $cv = new Cv();
        $mock = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getDocument')
            ->willReturn($cv);
        $this->clientMock
            ->expects($this->never())
            ->method('addDocument');
        $this->target->postUpdate($mock);
    }

    public function testShouldProcessOnPostUpdateEvent()
    {
        $job = new Job();
        $mock = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getDocument')
            ->willReturn($job);
        $this->clientMock
            ->expects($this->once())
            ->method('addDocument')
            ->with($this->isInstanceOf(\SolrInputDocument::class));
        $this->clientMock
            ->expects($this->once())
            ->method('commit');
        $this->clientMock
            ->expects($this->once())
            ->method('optimize');
        $this->target->postUpdate($mock);
    }
}
