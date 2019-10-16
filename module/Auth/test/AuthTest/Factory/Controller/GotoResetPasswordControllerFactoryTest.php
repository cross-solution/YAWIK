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

use Auth\Factory\Controller\GotoResetPasswordControllerFactory;
use CoreTest\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class GotoResetPasswordControllerFactoryTest extends TestCase
{
    /**
     * @var GotoResetPasswordControllerFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new GotoResetPasswordControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $gotoResetPasswordMock = $this->getMockBuilder('Auth\Service\GotoResetPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $sm->setService('Auth\Service\GotoResetPassword', $gotoResetPasswordMock);
        $sm->setService('Core/Log', $loggerMock);

        $controllerManager = new ControllerManager($sm);
        $sm->setService('ControllerManager', $controllerManager);

        $result = $this->testedObj->createService($sm);

        $this->assertInstanceOf('Auth\Controller\GotoResetPasswordController', $result);
    }
}
