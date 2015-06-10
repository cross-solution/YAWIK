<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Controller;

use Auth\Factory\Controller\RegisterConfirmationControllerFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class RegisterConfirmationControllerSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterConfirmationControllerFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new RegisterConfirmationControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $registerConfirmationServiceMock = $this->getMockBuilder('Auth\Service\RegisterConfirmation')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMock('Zend\Log\LoggerInterface');

        $sm->setService('Auth\Service\RegisterConfirmation', $registerConfirmationServiceMock);
        $sm->setService('Core/Log', $loggerMock);

        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Auth\Controller\RegisterConfirmationController', $result);
    }
}