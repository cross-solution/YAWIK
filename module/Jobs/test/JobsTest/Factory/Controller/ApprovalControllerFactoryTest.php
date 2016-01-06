<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use Jobs\Factory\Controller\ApprovalControllerFactory;
use Jobs\Controller;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;
use Auth\Entity\User;

/**
 * Class ApprovalControllerFactoryTest
 * @package JobsTest\Factory\Controller
 */
class ApprovalControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApprovalControllerFactory
     */
    private $testedObj;

    /**
     * The user entity fixture
     *
     * @var User
     */
    private $user;


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


        $user = new User();
        $user->setId('testUser');

        $auth = $this->getMockBuilder('Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $auth->expects($this->once())
             ->method('getUser')
             ->willReturn($user);


        $sm->setService('repositories', $repositoriesMock);
        $sm->setService('AuthenticationService', $auth);



        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Jobs\Controller\ApprovalController', $result);
    }
}