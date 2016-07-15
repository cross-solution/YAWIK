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
use CoreTestUtils\TestCase\FunctionalTestCase;
use Cv\Entity\Cv;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\CoordinatesInterface;
use Jobs\Entity\Job;
use Jobs\Entity\Location;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationName;
use Solr\Bridge\Manager;
use Solr\Bridge\Util;
use Solr\Listener\JobEventSubscriber;

/**
 * Test for Solr\Listener\JobEventSubscriber
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since   0.26
 * @covers  Solr\Listener\JobEventSubscriber
 * @requires extension solr
 * @package SolrTest\Listener
 */
class JobEventSubscriberTest extends FunctionalTestCase
{
    /**
     * @var \Solr\Listener\JobEventSubscriber
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $clientMock;

    public function setUp()
    {
        parent::setUp();
        $sl = $this->getApplicationServiceLocator();

        $managerMock = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $clientMock = $this->getMockBuilder(\SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $options = $this->getMockBuilder(ModuleOptions::class)
            ->setMethods(['getJobsPath'])
            ->getMock()
        ;
        $options->method('getJobsPath')->willReturn('/some/path');
        $managerMock->method('getOptions')->willReturn($options);

        $sl->setService('Solr/Manager', $managerMock);
        $managerMock->method('getClient')->willReturn($clientMock);
        $this->target = JobEventSubscriber::factory($sl);
        $this->managerMock = $managerMock;
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
        
        $orgName = new OrganizationName();
        $orgName->setName('some-name');
        $org = new Organization();
        $org->setOrganizationName($orgName);
        
        $job->setOrganization($org);
        
        $mock = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('getDocument')
            ->willReturn($job);

        $this->managerMock->expects($this->once())
            ->method('addDocument')
            ->with($this->isInstanceOf(\SolrInputDocument::class))
        ;
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
        $this->managerMock->expects($this->once())
            ->method('addDocument')
            ->with($this->isInstanceOf(\SolrInputDocument::class))
        ;
        $this->target->postUpdate($mock);
    }

    public function testGenerateInputDocument()
    {
        $date = new \DateTime();
        $dateStr = Util::convertDateTime($date);

        $job = new Job();
        $job
            ->setId('some-id')
            ->setTitle('some-title')
            ->setContactEmail('contact-email')
            ->setDateCreated($date)
            ->setDateModified($date)
            ->setDatePublishStart($date)
            ->setDatePublishEnd($date)
            ->setLink('http://test.link.org/job1')
            ->setLanguage('some-language')
        ;


        $document = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['addField'])
            ->getMock()
        ;

        $document->expects($this->any())
            ->method('addField')
            ->withConsecutive(
                ['id','some-id'],
                ['entityName','job'],
                ['title','some-title'],
                ['applicationEmail','contact-email'],
                ['link','http://test.link.org/job1'],
                ['html',$this->stringContains('http://test.link.org/job1')],
                ['dateCreated',$dateStr],
                ['dateModified',$dateStr],
                ['datePublishStart',$dateStr],
                ['datePublishEnd',$dateStr],
                ['isActive',false],
                ['lang','some-language']
            )
        ;
        $this->target->generateInputDocument($job,$document);
    }

    public function testProcessOrganization()
    {
        $job = $this->getMockBuilder(Job::class)
            ->getMock()
        ;
        $org = $this->getMockBuilder(Organization::class)
            ->getMock()
        ;
        $orgName = $this->getMockBuilder(OrganizationName::class)
            ->getMock()
        ;
        $orgImage = $this->getMockBuilder(OrganizationImage::class)
            ->getMock()
        ;

        $job->method('getOrganization')->willReturn($org);
        $org->method('getOrganizationName')->willReturn($orgName);
        $org->method('getImage')->willReturn($orgImage);

        $org->expects($this->once())
            ->method('getId')
            ->willReturn('some-id')
        ;

        $orgName->expects($this->once())
            ->method('getName')
            ->willReturn('some-name')
        ;
        $orgImage->expects($this->once())
            ->method('getUri')
            ->willReturn('some-uri')
        ;

        $document = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['addField'])
            ->getMock()
        ;
        $document
            ->expects($this->exactly(3))
            ->method('addField')
            ->withConsecutive(
                ['companyLogo','some-uri'],
                ['organizationName','some-name'],
                ['organizationId','some-id']
            )
        ;
        $this->target->processOrganization($job,$document);
    }

    public function testProcessLocation()
    {
        $job = $this->getMockBuilder(Job::class)->getMock();
        $location = $this->getMockBuilder(Location::class)->getMock();
        $coordinates = $this->getMockBuilder(CoordinatesInterface::class)->getMock();

        $job->expects($this->once())
            ->method('getLocations')
            ->willReturn([$location]);
        $location->expects($this->any())
            ->method('getCoordinates')
            ->willReturn($coordinates)
        ;
        $location->expects($this->once())
            ->method('getPostalCode')
            ->willReturn('postal-code')
        ;
        $location->expects($this->any())
            ->method('getRegion')
            ->willReturn('region-text')
        ;
        $coordinates->expects($this->once())
            ->method('getCoordinates')
            ->willReturn([1.2,2.1])
        ;
        $document = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['addField','addChildDocument'])
            ->getMock()
        ;
        $document->expects($this->any())
            ->method('addField')
        ;

        $this->target->processLocation($job,$document);
    }

    public function testConsoleIndex()
    {
        $target = $this->getMockBuilder(JobEventSubscriber::class)
            ->disableOriginalConstructor()
            ->setMethods(['updateIndex'])
            ->getMock()
        ;

        $target->expects($this->once())
            ->method('updateIndex')
        ;

        $job = new Job();
        $target->consoleIndex($job);
    }
}
