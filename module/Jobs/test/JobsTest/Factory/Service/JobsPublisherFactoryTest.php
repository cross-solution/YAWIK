<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Service;

use PHPUnit\Framework\TestCase;

use CoreTest\Bootstrap;

/**
 * Class JobsPublisherFactoryTest
 * @package Jobs\Factory\Service
 */
class JobsPublisherFactoryTest extends TestCase
{
    /**
     * @var JobsPublisherFactory
     */
    private $testedObj;

    private $mockJobsOptions;

    /**
     *
     */
    protected function setUp(): void
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

        $config->method('__isset')->with('multipostingTargetUri')->willReturn(true);
        $config->method('__get')->with('multipostingTargetUri')->willReturn('http://user:pass@host/path');
        $sm->setService('Jobs/Options', $config);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Core\Service\RestClient', $result);
    }
}
