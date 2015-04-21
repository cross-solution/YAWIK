<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Controller\SLFactory;

use Auth\Controller\SLFactory\ForgotPasswordControllerSLFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class ForgotPasswordControllerSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgotPasswordControllerSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new ForgotPasswordControllerSLFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $forgotPasswordMock = $this->getMockBuilder('Auth\Service\ForgotPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMock('Zend\Log\LoggerInterface');

        $sm->setService('Auth\Service\ForgotPassword', $forgotPasswordMock);
        $sm->setService('Core/Log', $loggerMock);

        $controllerManager = new ControllerManager();
        $controllerManager->setServiceLocator($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Auth\Controller\ForgotPasswordController', $result);
    }
}