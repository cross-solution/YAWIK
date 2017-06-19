<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use Jobs\Controller\ApprovalController;
use Jobs\Factory\Controller\ApprovalControllerFactory;
use Jobs\Form\OrganizationSelect;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

/**
 * Class ApprovalControllerFactoryTest
 * @package JobsTest\Factory\Controller
 * @covers \Jobs\Factory\Controller\ApprovalControllerFactory
 */
class ApprovalControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApprovalControllerFactory
     */
    private $testedObj;


    /**
     *
     */
    public function setUp()
    {
        $this->testedObj = new ApprovalControllerFactory();
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
        $formElementManager = $sm->get('FormElementManager');

        $organizationSelect = new OrganizationSelect();
        $formElementManager->setAllowOverride(true);
        $formElementManager->setService('Jobs/ActiveOrganizationSelect', $organizationSelect);

        $controllerManager = new ControllerManager($sm);

        $result = $this->testedObj->__invoke($sm,ApprovalController::class);

        $this->assertInstanceOf('Jobs\Controller\ApprovalController', $result);
    }
}
