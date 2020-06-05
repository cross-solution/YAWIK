<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace JobsTest\Factory\Filter;

use PHPUnit\Framework\TestCase;

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
class ChannelPricesFactoryTest extends TestCase
{
    /**
     * @testdox Implements \Laminas\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Laminas\ServiceManager\Factory\FactoryInterface', new ChannelPricesFactory());
    }

    /**
     * @testdox createService creates an ApplyUrl view helper and injects the required dependencies
     */
    public function testInvokation()
    {
        $provider=new ProviderOptions();

        $serviceManagerMock = $this->getMockBuilder('\Laminas\ServiceManager\ServiceManager')
                                   ->disableOriginalConstructor()
                                   ->getMock();


        $serviceManagerMock
            ->expects($this->once())
            ->method('get')
            ->with('Jobs/Options/Provider')
            ->willReturn($provider)
        ;

        $target = new ChannelPricesFactory();

        $service = $target->__invoke($serviceManagerMock, 'irrelevant');

        $this->assertInstanceOf('\Jobs\Filter\ChannelPrices', $service);
    }
}
