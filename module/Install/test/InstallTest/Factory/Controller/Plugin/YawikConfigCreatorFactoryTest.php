<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Factory\Controller\Plugin;

use Install\Factory\Controller\Plugin\YawikConfigCreatorFactory;
use Install\Filter\DbNameExtractor;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Install\Factory\Controller\Plugin\YawikConfigCreatorFactory
 * 
 * @covers \Install\Factory\Controller\Plugin\YawikConfigCreatorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Factory
 * @group Install.Factory.Controller
 * @group Install.Factory.Controller.Plugin
 */
class YawikConfigCreatorFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf(FactoryInterface::class, new YawikConfigCreatorFactory());
    }

    public function testCreatesAnUserCreatorPluginInstance()
    {
        $filters = $this->getMockBuilder('\Zend\Filter\FilterPluginManager')->disableOriginalConstructor()->getMock();
        $filters->expects($this->once())
                ->method('get')
                ->with('Install/DbNameExtractor')
                ->willReturn(new DbNameExtractor());

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->once())->method('get')->with('FilterManager')->willReturn($filters);

        $target = new YawikConfigCreatorFactory();
        $plugin = $target($services,'irrelevant');

        $this->assertInstanceOf('\Install\Controller\Plugin\YawikConfigCreator', $plugin);
    }
}