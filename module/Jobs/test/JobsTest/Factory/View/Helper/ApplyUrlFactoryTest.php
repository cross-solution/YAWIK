<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\View\Helper;

use Core\View\Helper\Params;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use Jobs\Factory\View\Helper\ApplyUrlFactory;

/**
 * Tests for ApplyUrl view helper factory
 *
 * @covers \Jobs\Factory\View\Helper\ApplyUrlFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.View
 * @group Jobs.Factory.View.Helper
 */
class ApplyUrlFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', new ApplyUrlFactory());
    }

    /**
     * @testdox createService creates an ApplyUrl view helper and injects the required dependencies
     */
    public function testServiceCreation()
    {
        $target = new ApplyUrlFactory();

        $urlHelper = new Url();
        $translateHelper = new Translate();
        $paramsHelper = new Params(new MvcEvent());
        $serverUrl = new ServerUrl();

        $helpers = $this->getMockBuilder('\Zend\View\HelperPluginManager')
                        ->disableOriginalConstructor()
                        ->getMock();

        $helpers->expects($this->exactly(4))
                ->method('get')
                ->withConsecutive(
                    array('url'), array('translate'), array('params'), array('serverUrl')
                )
                ->will($this->onConsecutiveCalls($urlHelper, $translateHelper, $paramsHelper, $serverUrl));
		
        $sm = $this->getMockBuilder(ServiceManager::class)
	        ->disableOriginalConstructor()
	        ->getMock();
	    $sm->expects($this->once())
		    ->method('get')
		    ->with('ViewHelperManager')
		    ->willReturn($helpers);
	    
        $service = $target->__invoke($sm,'irrelevant');

        $this->assertInstanceOf('\Jobs\View\Helper\ApplyUrl', $service);
        $this->assertAttributeSame($urlHelper, 'urlHelper', $service);
        $this->assertAttributeSame($translateHelper, 'translateHelper', $service);
        $this->assertAttributeSame($paramsHelper, 'paramsHelper', $service);
        $this->assertAttributeSame($serverUrl, 'serverUrlHelper', $service);
    }
}
