<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Service;

use Test\Bootstrap;

/**
 * Class JobsPublisherFactoryTest
 * @package Jobs\Factory\Service
 */
class JobsPublisherFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobsPublisherFactory
     */
    private $testedObj;

    private $mockJobsOptions;

    /**
     *
     */
    public function setUp()
    {
        $this->testedObj = new JobsPublisherFactory();
        $this->mockJobsOptions = $this->getMockBuilder('Jobs\Options\ModuleOptions')
                     ->disableOriginalConstructor()
                     ->getMock();

    }

    /**
     *
     */
    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $config = $this->mockJobsOptions;

        $config->method('__isset')->with('multipostingTargetUri')->willReturn(True);
        $config->method('__get')->with('multipostingTargetUri')->willReturn('http://user:pass@host/path');
        $sm->setService('Jobs/Options',$config);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Core\Service\RestClient', $result);
    }
}