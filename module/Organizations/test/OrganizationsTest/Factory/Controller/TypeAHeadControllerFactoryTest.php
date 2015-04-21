<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace OrganizationsTest\Factory\Controller;

use Organizations\Factory\Controller\TypeAHeadControllerFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

/**
 * @group Organizations
 * @group Organizations.Controller
 * @group Organizations.Controller.SLFactory
 *
 */
class TypeAHeadControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeAHeadControllerFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new TypeAHeadControllerFactory();
    }

    /**
     */
    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $organizationRepositoryMock = $this->getMockBuilder('Organizations\Repository\Organization')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Organizations/Organization')
            ->willReturn($organizationRepositoryMock);

        $sm->setService('repositories', $repositoriesMock);

        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Organizations\Controller\TypeAHeadController', $result);
        $this->assertAttributeSame($organizationRepositoryMock, 'organizationRepository', $result);
    }
}
