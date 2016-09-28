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
use Solr\Options\ModuleOptions as ConnectOption;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Test for SolrTest\Bridge\Manager
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since   0.26
 * @covers  Solr\Bridge\Manager
 * @requires extension solr
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

    public function testGetOptions()
    {
        $this->assertEquals($this->option,$this->target->getOptions());
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
}
