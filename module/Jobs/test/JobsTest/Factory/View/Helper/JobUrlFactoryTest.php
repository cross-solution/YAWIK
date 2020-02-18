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

use PHPUnit\Framework\TestCase;

use Core\View\Helper\Params;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\ServerUrl;
use Laminas\View\Helper\Url;
use Jobs\Factory\View\Helper\JobUrlFactory;

/**
 * Tests for JobUrl view helper factory
 *
 * @covers \Jobs\Factory\View\Helper\JobUrlFactory
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.View
 * @group Jobs.Factory.View.Helper
 */
class JobUrlFactoryTest extends TestCase
{

    /**
     * @testdox Implements \Laminas\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Laminas\ServiceManager\Factory\FactoryInterface', new JobUrlFactory());
    }

    /**
     * @testdox createService creates an JobUrl view helper and injects the required dependencies
     */
    public function testServiceCreation()
    {
        $target = new JobUrlFactory();

        $urlHelper = new Url();
        $paramsHelper = new Params(new MvcEvent());
        $serverUrl = new ServerUrl();
        
        $helpers = $this->getMockBuilder('\Laminas\View\HelperPluginManager')
                        ->disableOriginalConstructor()
                        ->getMock();

        $helpers->expects($this->exactly(3))
                ->method('get')
                ->withConsecutive(
                    array('url'),
                    array('params'),
                    array('serverUrl')
                )
                ->will($this->onConsecutiveCalls($urlHelper, $paramsHelper, $serverUrl));
        $sm = $this
            ->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $sm->expects($this->once())
            ->method('get')
            ->with('ViewHelperManager')
            ->willReturn($helpers)
        ;
        $service = $target->__invoke($sm, 'irrelevant');

        $this->assertInstanceOf('\Jobs\View\Helper\JobUrl', $service);
        $this->assertAttributeSame($urlHelper, 'urlHelper', $service);
        $this->assertAttributeSame($paramsHelper, 'paramsHelper', $service);
        $this->assertAttributeSame($serverUrl, 'serverUrlHelper', $service);
    }
}
