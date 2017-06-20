<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Factory\Filter;

use Jobs\Factory\Filter\ChannelPricesFactory;
use Jobs\Options\ChannelOptions;
use Jobs\Options\ProviderOptions;

/**
 * Tests for ApplyUrl view helper factory
 *
 * @covers \Jobs\Factory\Filter\ChannelPricesFactory
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Factory
 * @group  Jobs.Factory.Filter
 */
class ChannelPricesFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', new ChannelPricesFactory());
    }

    /**
     * @testdox createService creates an ApplyUrl view helper and injects the required dependencies
     */
    public function testInvokation()
    {
        $provider=new ProviderOptions();

        $serviceManagerMock = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                                   ->disableOriginalConstructor()
                                   ->getMock();


        $serviceManagerMock
	        ->expects($this->once())
	        ->method('get')
	        ->with('Jobs/Options/Provider')
	        ->willReturn($provider)
        ;
        
        $target = new ChannelPricesFactory();

        $service = $target->__invoke($serviceManagerMock,'irrelevant');

        $this->assertInstanceOf('\Jobs\Filter\ChannelPrices', $service);
    }
}
