<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Controller;

use Jobs\Factory\Controller\JobboardControllerFactory;
use Jobs\Controller;
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
    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $jobRepositoryMock = $this->getMockBuilder('Jobs\Repository\Job')
            ->disableOriginalConstructor()
            ->getMock();

        $geoOptionsMock = $this->getMockBuilder('Geo\Options')
                               ->disableOriginalConstructor()
                               ->getMock();

        $formElementManagerMock = $this->getMockBuilder('formElementManager')
                              ->disableOriginalConstructor()
                              ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $repositoriesMock->expects($this->once())
                         ->method('get')
                         ->with('GeoOptions')
                         ->willReturn($geoOptionsMock);




        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Jobs/Job')
            ->willReturn($jobRepositoryMock);

        $sm->setService('repositories', $repositoriesMock);
        $sm->setService('formElementManager', $formElementManagerMock);

        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Jobs\Controller\JobboardController', $result);
    }
}