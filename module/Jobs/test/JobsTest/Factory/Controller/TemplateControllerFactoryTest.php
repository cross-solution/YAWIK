<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use Jobs\Factory\Controller\TemplateControllerFactory;
use Jobs\Controller;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

/**
 * Class TemplateControllerFactoryTest
 * @package JobsTest\Factory\Controller
 */
class TemplateControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateControllerFactory
     */
    private $testedObj;

    /**
     *
     */
    public function setUp()
    {
        $this->testedObj = new TemplateControllerFactory();
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

        $sm->setService('repositories', $repositoriesMock);

        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Jobs\Controller\TemplateController', $result);
    }
}