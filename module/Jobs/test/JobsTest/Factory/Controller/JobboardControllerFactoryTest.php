<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use Jobs\Controller\JobboardController;
use Jobs\Factory\Controller\JobboardControllerFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

/**
 * Class JobboardControllerFactoryTest
 * @package JobsTest\Factory\Controller
 */
class JobboardControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobboardControllerFactory
     */
    private $testedObj;

    /**
     *
     */
    public function setUp()
    {
        $this->testedObj = new JobboardControllerFactory();
    }

    /**
     *
     */
    public function testInvoke()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $jobRepositoryMock = $this->getMockBuilder('Jobs\Repository\Job')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Jobs/Job')
            ->willReturn($jobRepositoryMock);

        $sm->setService('repositories', $repositoriesMock);
        $result = $this->testedObj->__invoke($sm,JobboardController::class);

        $this->assertInstanceOf('Jobs\Controller\JobboardController', $result);
    }
}
