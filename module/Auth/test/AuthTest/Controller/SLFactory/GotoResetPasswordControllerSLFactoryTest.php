<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller\SLFactory;

use Auth\Controller\SLFactory\GotoResetPasswordControllerSLFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class GotoResetPasswordControllerSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GotoResetPasswordControllerSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new GotoResetPasswordControllerSLFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $gotoResetPasswordMock = $this->getMockBuilder('Auth\Service\GotoResetPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMock('Zend\Log\LoggerInterface');

        $sm->setService('Auth\Service\GotoResetPassword', $gotoResetPasswordMock);
        $sm->setService('Core/Log', $loggerMock);

        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Auth\Controller\GotoResetPasswordController', $result);
    }
}