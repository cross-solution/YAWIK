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

use Auth\Entity\Filter\CredentialFilter;
use Install\Factory\Controller\Plugin\UserCreatorFactory;
use Install\Filter\DbNameExtractor;

/**
 * Tests for \Install\Factory\Controller\Plugin\UserCreatorFactory
 * 
 * @covers \Install\Factory\Controller\Plugin\UserCreatorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Factory
 * @group Install.Factory.Controller
 * @group Install.Factory.Controller.Plugin
 */
class UserCreatorFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', new UserCreatorFactory());
    }

    public function testCreatesAnUserCreatorPluginInstance()
    {
        $filters = $this->getMockBuilder('\Zend\Filter\FilterPluginManager')->disableOriginalConstructor()->getMock();
        $filters->expects($this->exactly(2))
                ->method('get')
                ->withConsecutive(
                    array('Install/DbNameExtractor'),
                    array('Auth/CredentialFilter')
                )
                ->will($this->onConsecutiveCalls(new DbNameExtractor(), new CredentialFilter()));

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services->expects($this->once())->method('get')->with('FilterManager')->willReturn($filters);

        $plugins = $this->getMockBuilder('\Zend\Mvc\Controller\PluginManager')->disableOriginalConstructor()->getMock();
        $plugins->expects($this->once())->method('getServiceLocator')->willReturn($services);

        $target = new UserCreatorFactory();
        $plugin = $target->createService($plugins);

        $this->assertInstanceOf('\Install\Controller\Plugin\UserCreator', $plugin);
    }
}