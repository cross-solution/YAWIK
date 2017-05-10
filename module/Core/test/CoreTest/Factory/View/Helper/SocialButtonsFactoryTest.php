<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Factory\View\Helper;

use Auth\Options\ModuleOptions;
use Core\Factory\View\Helper\SocialButtonsFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;

/**
 * Class SocialButtonsFactoryTest
 *
 * @package Core\Factory\View\Helper
 * @covers Core\Factory\View\Helper\SocialButtonsFactory
 */
class SocialButtonsFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ServiceManagerMockTrait;

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', new SocialButtonsFactory());
    }

    /**
     * @testdox createService creates an ApplyUrl view helper and injects the required dependencies
     */
    public function testServiceCreation()
    {
        $serviceLocator = $this->getMockBuilder('\Zend\View\HelperPluginManager')->disableOriginalConstructor()->getMock();

        $options = new ModuleOptions();
        $config = ['testwert'];

        $HauptServiceLocator =  $this->getMockBuilder('Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $HauptServiceLocator->expects($this->exactly(2))->method('get')->withConsecutive(['Auth/Options'],['Config'])->will($this->onConsecutiveCalls($options, $config));

        $serviceLocator->expects($this->exactly(1))->method('getServiceLocator')->willReturn($HauptServiceLocator);

        $target = new SocialButtonsFactory();

        $helper = $target->createService($serviceLocator);

        $this->assertInstanceOf("Core\View\Helper\SocialButtons",$helper);
        $this->assertAttributeSame($options,'options',$helper);
        $this->assertAttributeEquals($config,'config',$helper);

    }
}