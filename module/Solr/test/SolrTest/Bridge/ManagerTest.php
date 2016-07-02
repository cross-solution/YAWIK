<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Bridge;


use Solr\Bridge\Manager;
use Solr\Exception\ServerException;
use Solr\Options\ModuleOptions as ConnectOption;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Test for SolrTest\Bridge\Manager
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.27
 * @covers  Solr\Bridge\Manager
 * @package SolrTest\Bridge
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock for ConnectOption class
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $option;

    /**
     * @var Manager
     */
    protected $target;

    public function setUp()
    {
        $option = $this->getMockBuilder(ConnectOption::class)
            ->getMock()
        ;

        $this->option = $option;
        $this->target = new Manager($option);
    }

    public function testFactory()
    {
        $mock = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock()
        ;
        $mock
            ->expects($this->once())
            ->method('get')
            ->with('Solr/Options/Module')
            ->willReturn($this->option)
        ;
        $this->assertInstanceOf(
            Manager::class,
            Manager::factory($mock),
            '::factory() should create object properly'
        );
    }

    public function testGetClient()
    {
        $option = $this->option;

        $option
            ->expects($this->once())
            ->method('isSecure')
            ->willReturn(true)
        ;
        $option
            ->expects($this->once())
            ->method('getHostname')
            ->willReturn('some_hostname')
        ;


        $this->assertInstanceOf(
            \SolrClient::class,
            $client = $this->target->getClient(),
            '::getClient() should create client properly'
        );

        $createdOptions = $client->getOptions();
        $this->assertEquals(true,$createdOptions['secure']);
        $this->assertEquals('some_hostname',$createdOptions['hostname']);
    }

    /**
     * @expectedException           \Solr\Exception\ServerException
     * @expectedExceptionMessage    Can not add document to server!
     */
    public function testAddDocument()
    {
        $client = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['addDocument','commit','optimize'])
            ->getMock()
        ;
        $mock = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getClient'])
            ->getMock()
        ;
        $mock->expects($this->any())
            ->method('getClient')
            ->with('/solr')
            ->willReturn($client)
        ;

        $document = new \SolrInputDocument();
        $client->expects($this->exactly(2))
            ->method('addDocument')
            ->withConsecutive([$document],[$document])
            ->willReturnOnConsecutiveCalls(true,$this->throwException(new \Exception()))
        ;
        $client->expects($this->once())
            ->method('commit')
        ;
        $client->expects($this->once())
            ->method('optimize')
        ;

        $mock->addDocument($document);
        $mock->addDocument($document);//should throw exception now
    }
}
