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

class ConnectionOptionFactoryTest extends FunctionalTestCase
{
    public function testCreateService()
    {
        $sm = $this->getApplicationServiceLocator();
        $config = $sm->get('Configuration');
        $options = $config['solr']['connection'];

        /**
         * @var Connection $connectOption
         */
        $connectOption = $sm->get('Solr/Options/Connection');

        $this->assertEquals($options['hostname'], $connectOption->getHostname());
        $this->assertEquals($options['port'], $connectOption->getPort());
        $this->assertEquals($options['path'], $connectOption->getPath());
        $this->assertEquals($options['username'], $connectOption->getUsername());
        $this->assertEquals($options['password'], $connectOption->getPassword());
    }
}
