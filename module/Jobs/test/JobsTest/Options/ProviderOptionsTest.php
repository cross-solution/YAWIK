<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace JobsTest\Options;

use Jobs\Options\ChannelOptions;
use Jobs\Options\ProviderOptions;

class ProviderOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProviderOptions $options
     */
    protected $options;

    public function setUp()
    {
        $options       = new ProviderOptions;
        $this->options = $options;
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('Jobs\Options\ProviderOptions', $this->options);
        $this->assertEquals([], $this->options->getChannels());
    }

    /**
     * @covers Jobs\Options\ProviderOptions::getIterator
     */
    public function testGetIterator()
    {
        $expected = new \ArrayIterator([]);
        $this->assertEquals($expected, $this->options->getIterator());
    }

    /**
     * @covers Jobs\Options\ProviderOptions::addChannel
     * @covers Jobs\Options\ProviderOptions::getChannel
     */
    public function testAddGetChannel()
    {
        $channel = new ChannelOptions();
        $channel->setKey('test')->setLabel('label');
        $this->options->addChannel($channel);
        $this->assertEquals($channel, $this->options->getChannel('test'));
        $this->assertEquals(null, $this->options->getChannel('nonexistance'));
    }

    /**
     * @covers Jobs\Options\ProviderOptions::getChannels
     */
    public function testGetChannels()
    {
        $channel = new ChannelOptions();
        $channel->setKey('test')->setLabel('label');
        $this->options->addChannel($channel);
        $this->assertEquals(['test'=>$channel], $this->options->getChannels());
    }
}

