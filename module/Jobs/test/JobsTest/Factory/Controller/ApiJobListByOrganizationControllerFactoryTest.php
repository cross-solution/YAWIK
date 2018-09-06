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

        $apiJobDehydratorMock = $this->getMockBuilder('Jobs\Model\ApiJobDehydrator')
                                ->disableOriginalConstructor()
                                ->getMock();

        $sm->setService('repositories', $repositoriesMock);
        $sm->setService('Jobs\Model\ApiJobDehydrator', $apiJobDehydratorMock);

	    $result = $this->testedObj->__invoke($sm,ApiJobListByOrganizationController::class);
        $this->assertInstanceOf('Jobs\Controller\ApiJobListByOrganizationController', $result);
    }
}
