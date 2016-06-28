<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Factory;


use CoreTestUtils\TestCase\FunctionalTestCase;
use Solr\Options\Connection;

class SolrClientFactoryTest extends FunctionalTestCase
{
    public function testCreateService()
    {
        $sm = $this->getApplicationServiceLocator();
        $mock = $this->getMockBuilder(Connection::class)
            ->getMock();
        $sm->setService('Solr/Options/Connection', $mock);

        $mock
            ->expects($this->once())
            ->method('getHostname')
            ->willReturn('localhost');
        $mock
            ->expects($this->once())
            ->method('getPort')
            ->willReturn(80);

        $mock
            ->expects($this->once())
            ->method('getPath')
            ->willReturn('/solr');

        $mock
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('someusername');

        $mock
            ->expects($this->once())
            ->method('getPassword')
            ->willReturn('password');

        $client = $sm->get('Solr/Client');
        $this->assertInstanceOf('\SolrClient', $client);
    }
}
