<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Controller;

use PHPUnit\Framework\TestCase;

use Auth\Factory\Controller\ForgotPasswordControllerFactory;
use CoreTest\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class ForgotPasswordControllerFactoryTest extends TestCase
{
    /**
     * @var ForgotPasswordControllerFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new ForgotPasswordControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $forgotPasswordMock = $this->getMockBuilder('Auth\Service\ForgotPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $sm->setService('Auth\Service\ForgotPassword', $forgotPasswordMock);
        $sm->setService('Core/Log', $loggerMock);
        $controllerManager = new ControllerManager($sm);
        $sm->setService('ControllerManager', $controllerManager);

        $result = $this->testedObj->createService($sm);

        $this->assertInstanceOf('Auth\Controller\ForgotPasswordController', $result);
    }
}
