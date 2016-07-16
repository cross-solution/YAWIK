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
        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', new YawikConfigCreatorFactory());
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

        $plugins = $this->getMockBuilder('\Zend\Mvc\Controller\PluginManager')->disableOriginalConstructor()->getMock();
        $plugins->expects($this->once())->method('getServiceLocator')->willReturn($services);

        $target = new YawikConfigCreatorFactory();
        $plugin = $target->createService($plugins);

        $this->assertInstanceOf('\Install\Controller\Plugin\YawikConfigCreator', $plugin);
    }
}