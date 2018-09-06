<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use Jobs\Factory\Controller\ApiJobListByOrganizationControllerFactory;
use Zend\Mvc\Controller\ControllerManager;
use Test\Bootstrap;

/**
 * Class TApiJobListByOrganizationControllerFactoryTest
 * @package JobsTest\Factory\Controller
 */
class ApiJobListByOrganizationControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiJobListByOrganizationControllerFactory
     */
    private $testedObj;

    /**
     *
     */
    public function setUp()
    {
        $this->testedObj = new ApiJobListByOrganizationControllerFactory();
    }

    /**
     *
     */
    public function testCreateService()
    {
        $serviceManager = clone Bootstrap::getServiceManager();
        $serviceManager->setAllowOverride(true);

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

        $apiJobDehydratorMock = $this->getMockBuilder('Jobs\Model\ApiJobDehydrator')
                                ->disableOriginalConstructor()
                                ->getMock();

        $serviceManager->setService('repositories', $repositoriesMock);
        $serviceManager->setService('Jobs\Model\ApiJobDehydrator', $apiJobDehydratorMock);

	    $result = $this->testedObj->__invoke($serviceManager,ApiJobListByOrganizationController::class);
        $this->assertInstanceOf('Jobs\Controller\ApiJobListByOrganizationController', $result);
    }
}
